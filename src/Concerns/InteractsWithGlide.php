<?php

namespace Awcodes\Curator\Concerns;

use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;

trait InteractsWithGlide
{
    protected string $glideCachePathPrefix = '.cache';

    protected int $glideMaxImageSize = 2000 * 2000;

    protected string $glideDriver = 'gd';

    protected array|null $gliderFallbacks = [];

    protected Server|ServerFactory|null $glideServer = null;

    protected string $glideSourcePathPrefix = 'public';

    public function getGlideCachePathPrefix(): string
    {
        return $this->glideCachePathPrefix;
    }

    public function getGlideDriver(): string
    {
        return $this->glideDriver;
    }

    public function getGliderFallback(string $key): ?array
    {
        return collect($this->getGliderFallbacks())->where('key', $key)->sole();
    }

    public function getGliderFallbacks(): ?array
    {
        return collect($this->gliderFallbacks)->map(function ($preset) {
            return $preset->getFallback();
        })->toArray();
    }

    public function getGlideMaxImageSize(): int
    {
        return $this->glideMaxImageSize;
    }

    public function getGlideServer(): Server|ServerFactory
    {
        if (!$this->glideServer) {
            return ServerFactory::create([
                'driver' => $this->getGlideDriver(),
                'response' => new LaravelResponseFactory(app('request')),
                'source' => storage_path('app'),
                'source_path_prefix' => $this->getGlideSourcePathPrefix(),
                'cache' => storage_path('app'),
                'cache_path_prefix' => $this->getGlideCachePathPrefix(),
                'max_image_size' => $this->getGlideMaxImageSize(),
            ]);
        }

        return $this->glideServer;
    }

    public function getGlideSourcePathPrefix(): string
    {
        return $this->glideSourcePathPrefix;
    }

    public function glideCachePathPrefix(string $prefix = '.cache'): static
    {
        $this->glideCachePathPrefix = $prefix;

        return $this;
    }

    public function glideDriver(string $driver = 'gd'): static
    {
        $this->glideDriver = $driver;

        return $this;
    }

    public function glideMaxImageSize(int $size): static
    {
        $this->glideMaxImageSize = $size;

        return $this;
    }

    public function gliderFallbacks(array|null $fallbacks): static
    {
        $this->gliderFallbacks = $fallbacks;

        return $this;
    }

    public function glideServer(Server|ServerFactory|null $server): static
    {
        $this->glideServer = $server;

        return $this;
    }

    public function glideSourcePathPrefix(string $prefix = 'public'): static
    {
        $this->glideSourcePathPrefix = $prefix;

        return $this;
    }
}