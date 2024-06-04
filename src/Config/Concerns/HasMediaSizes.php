<?php

namespace Awcodes\Curator\Config\Concerns;

use Awcodes\Curator\Concerns\UrlProvider;
use Closure;
use Illuminate\Support\Number;

trait HasMediaSizes
{
    protected UrlProvider | Closure | null $urlProvider = null;

    public function urlProvider(UrlProvider | Closure $provider): static
    {
        $this->urlProvider = $provider;

        return $this;
    }

    public function getUrlProvider(): UrlProvider
    {
        $provider = $this->evaluate($this->urlProvider) ?? config('curator.url_provider');

        return app($provider);
    }

    public function sizeForHumans(int $size, ?int $precision = 2): string
    {
        return Number::fileSize($size, $precision);
    }
}
