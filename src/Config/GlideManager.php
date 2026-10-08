<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Config\Concerns\HasGliderFallbacks;
use Awcodes\Curator\Glide\GliderFallback;
use Awcodes\Curator\Glide\SymfonyResponseFactory;
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

    protected array $serverConfig;

    protected string $token;

    protected ?string $basePath = null;

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
            'cache_path_prefix' => '.cache',
            'max_image_size' => 2000 * 2000,
        ];
    }
}
