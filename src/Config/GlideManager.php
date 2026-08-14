<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Config\Concerns\HasGliderFallbacks;
use Awcodes\Curator\Glide\SymfonyResponseFactory;
use Exception;
use Filament\Support\Concerns\EvaluatesClosures;
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

    public function getServer(): Server
    {
        return ServerFactory::create($this->serverConfig ?? $this->getDefaultServerConfig());
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

    private function getDefaultServerConfig(): array
    {
        return [
            'response' => new SymfonyResponseFactory(app('request')),
            'source' => storage_path('app'),
            'source_path_prefix' => 'public',
            'cache' => storage_path('app'),
            'cache_path_prefix' => '.cache',
            'max_image_size' => 2000 * 2000,
            'base_url' => $this->getBasePath(),
        ];
    }
}
