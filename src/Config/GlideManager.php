<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Config\Concerns\HasGliderFallbacks;
use Awcodes\Curator\Glide\SymfonyResponseFactory;
use Awcodes\Curator\Models\Media;
use Closure;
use DateTimeInterface;
use Filament\Support\Concerns\EvaluatesClosures;
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
     * A server config registered through serverConfig() is used as given,
     * except for base_url: the controller passes the media's own path, and
     * Glide would strip a leading base_url from it and serve a different file
     * for media stored in a directory of that name.
     */
    public function getServer(): Server
    {
        return ServerFactory::create([
            ...($this->serverConfig ?? $this->getDefaultServerConfig()),
            'base_url' => '',
        ]);
    }

    public function getBasePath(): string
    {
        return $this->basePath ?? 'curator';
    }

    public function getToken(): string
    {
        return config('curator.glide_token');
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

    protected function buildSignedUrl(string $path, array $params): string
    {
        $urlBuilder = UrlBuilderFactory::create($this->getBasePath(), $this->getToken());

        return $urlBuilder->getUrl($path, $params);
    }

    private function getDefaultServerConfig(): array
    {
        return [
            'response' => new SymfonyResponseFactory(app('request')),
            'source' => storage_path('app'),
            'source_path_prefix' => 'public',
            'cache' => storage_path('app'),
            'cache_path_prefix' => '.cache',
            'max_image_size' => 2000 * 2000,
        ];
    }
}
