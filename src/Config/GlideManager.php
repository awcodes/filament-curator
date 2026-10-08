<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Config\Concerns\HasGliderFallbacks;
use Awcodes\Curator\Glide\GliderFallback;
use Awcodes\Curator\Glide\SymfonyResponseFactory;
use Awcodes\Curator\Models\Media;
use Closure;
use DateTimeInterface;
use Exception;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Glide\Server;
use League\Glide\ServerFactory;
use League\Glide\Urls\UrlBuilderFactory;

class GlideManager
{
    use EvaluatesClosures;
    use HasGliderFallbacks;

    public const EXPIRES_PARAMETER = 'expires';

    public const DISK_PARAMETER = 'disk';

    protected array $serverConfig;

    protected string $token;

    protected ?string $basePath = null;

    /**
     * Set while a callback passed to withTemporaryUrls() runs.
     *
     * @var array{disk: string|null, expires: int}|null
     */
    protected ?array $temporaryUrlScope = null;

    /**
     * Each request starts from a copy of the booted manager, so it gets its own fallbacks to change.
     */
    public function __clone(): void
    {
        $this->gliderFallbacks = array_map(fn (GliderFallback $fallback): GliderFallback => clone $fallback, $this->gliderFallbacks);
    }

    public static function configure(): static
    {
        return app(static::class);
    }

    public function serverConfig(array $config): static
    {
        $this->serverConfig = $config;

        return $this;
    }

    public function basePath(string $basePath): static
    {
        $this->basePath = (string) Str::of($basePath)->trim('/');

        return $this;
    }

    /**
     * Glide has to read from the disk the media was stored on. A server config
     * registered through serverConfig() is used as given, except for base_url:
     * the controller passes a path relative to the disk, and Glide would strip
     * a leading base_url from it and serve a different file for media stored
     * in a directory of that name.
     */
    public function getServer(?string $disk = null): Server
    {
        if (! isset($this->serverConfig)) {
            return ServerFactory::create($this->getDefaultServerConfig($disk));
        }

        // The response factory needs the current request, so a config set once at boot gets a fresh one per call.
        return ServerFactory::create([
            'response' => new SymfonyResponseFactory(app('request')),
            ...$this->serverConfig,
            'base_url' => '',
        ]);
    }

    public function getBasePath(): string
    {
        return $this->basePath ?? 'curator';
    }

    /**
     * @throws Exception
     */
    public function getToken(): string
    {
        $token = config('curator.glide_token');

        // curator:install writes this into the .env of the machine it ran on, so
        // a second checkout or a fresh deploy target reliably arrives without
        // one. Say which knob is missing rather than raising a return type error.
        if (blank($token)) {
            throw new Exception(message: 'Curator has no Glide token. Run `php artisan curator:token` to generate one, or set CURATOR_GLIDE_TOKEN in this environment.');
        }

        return $token;
    }

    public function getUrl(string $path, ?array $params = []): string
    {
        if ($this->temporaryUrlScope !== null) {
            return $this->getTemporaryUrl($path, $params ?? [], $this->temporaryUrlScope['expires'], $this->temporaryUrlScope['disk']);
        }

        return $this->buildSignedUrl($path, $params ?? []);
    }

    /**
     * A signed URL that the media route refuses once it expires. Media that
     * isn't public is only served through one of these, and never with
     * headers that let a shared cache keep it.
     */
    public function getTemporaryUrl(string $path, array $params = [], DateTimeInterface | int | null $expiration = null, ?string $disk = null): string
    {
        $expiration ??= $this->getTemporaryUrlExpiration();

        $params[self::EXPIRES_PARAMETER] = $expiration instanceof DateTimeInterface ? $expiration->getTimestamp() : $expiration;

        if (filled($disk)) {
            $params[self::DISK_PARAMETER] = $disk;
        }

        return $this->buildSignedUrl($path, $params);
    }

    /**
     * Glide URLs for the media, temporary when the media isn't public. Public
     * media keeps the permanent URL it has always had.
     *
     * @param  Media|array{path: string, disk?: string|null, visibility?: string|null}  $media
     */
    public function getMediaUrl(Media | array $media, array $params = []): string
    {
        $attributes = $media instanceof Media ? $media->only(['path', 'disk', 'visibility']) : $media;

        if (Media::isPublicVisibility($attributes['visibility'] ?? null)) {
            return $this->getUrl($attributes['path'], $params);
        }

        return $this->getTemporaryUrl($attributes['path'], $params, disk: $attributes['disk'] ?? null);
    }

    /**
     * Every Glide URL built while the callback runs, including those built by
     * a custom URL provider through GlideBuilder or this manager, is temporary
     * and names the disk.
     *
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    public function withTemporaryUrls(?string $disk, Closure $callback, DateTimeInterface | int | null $expiration = null): mixed
    {
        $previous = $this->temporaryUrlScope;

        $expiration ??= $this->getTemporaryUrlExpiration();

        $this->temporaryUrlScope = [
            'disk' => $disk,
            'expires' => $expiration instanceof DateTimeInterface ? $expiration->getTimestamp() : $expiration,
        ];

        try {
            return $callback();
        } finally {
            $this->temporaryUrlScope = $previous;
        }
    }

    /**
     * Rounded up to the minute, so a page that re-renders (a Livewire
     * component, say) keeps the same URLs for a while instead of downloading
     * every image again on each request.
     */
    public function getTemporaryUrlExpiration(): DateTimeInterface
    {
        $minutes = max(1, (int) config('curator.temporary_url_expiration', 5));

        $expiration = now()->addMinutes($minutes);

        return $expiration->second === 0 ? $expiration : $expiration->startOfMinute()->addMinute();
    }

    /**
     * The disks' own cache folders keep two files that share a path on
     * different disks from serving each other's transformed images. The
     * default disk keeps the folder it has always used.
     */
    public function getCachePathPrefix(?string $disk = null): string
    {
        if (blank($disk) || $disk === config('curator.default_disk')) {
            return '.cache';
        }

        return '.cache/.disks/' . Str::slug($disk) . '-' . substr(hash('xxh3', $disk), 0, 8);
    }

    protected function buildSignedUrl(string $path, array $params): string
    {
        $urlBuilder = UrlBuilderFactory::create($this->getBasePath(), $this->getToken());

        return $urlBuilder->getUrl($path, $params);
    }

    private function getDefaultServerConfig(?string $disk = null): array
    {
        return [
            'response' => new SymfonyResponseFactory(app('request')),
            // Media paths are relative to their disk, so read through that disk
            // rather than assuming storage/app/public: uploads to any other disk
            // (the local disk of a fresh Laravel app, S3, ...) otherwise 500.
            'source' => Storage::disk($disk ?? config('curator.default_disk'))->getDriver(),
            'cache' => storage_path('app'),
            'cache_path_prefix' => $this->getCachePathPrefix($disk),
            'max_image_size' => 2000 * 2000,
        ];
    }
}
