<?php

namespace Awcodes\Curator\Config;

use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Str;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;
use League\Glide\Urls\UrlBuilderFactory;

class GlideManager
{
    use EvaluatesClosures;
    use Concerns\HasGliderFallbacks;

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
        return ServerFactory::create($this->config ?? $this->getDefaultServerConfig());
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
        $urlBuilder = UrlBuilderFactory::create($this->getBasePath(), $this->getToken());

        return $urlBuilder->getUrl($path, $params);
    }

    private function getDefaultServerConfig(): array
    {
        return [
            'driver' => 'gd',
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