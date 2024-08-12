<?php

namespace Awcodes\Curator\Concerns;

use Awcodes\Curator\Glide\DefaultServerFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;

trait HasGlideSettings
{
    protected ?array $gliderFallbacks = [];

    protected Server | ServerFactory | null $glideServer = null;

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

    public function getGlideServer(): Server | ServerFactory
    {
        if (! $this->glideServer) {
            return (new DefaultServerFactory)->getFactory();
        }

        return $this->glideServer;
    }

    public function gliderFallbacks(?array $fallbacks): static
    {
        $this->gliderFallbacks = $fallbacks;

        return $this;
    }

    public function glideServer(Server | ServerFactory | null $server): static
    {
        $this->glideServer = $server;

        return $this;
    }
}
