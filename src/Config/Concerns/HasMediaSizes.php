<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config\Concerns;

use Awcodes\Curator\Concerns\UrlProvider;
use Closure;
use Illuminate\Support\Number;

trait HasMediaSizes
{
    /** @var UrlProvider|class-string<UrlProvider>|Closure|null */
    protected UrlProvider | string | Closure | null $urlProvider = null;

    /**
     * @param  UrlProvider|class-string<UrlProvider>|Closure  $provider
     */
    public function urlProvider(UrlProvider | string | Closure $provider): static
    {
        $this->urlProvider = $provider;

        return $this;
    }

    public function getUrlProvider(): UrlProvider
    {
        $provider = $this->evaluate($this->urlProvider) ?? config('curator.url_provider');

        return $provider instanceof UrlProvider ? $provider : app($provider);
    }

    public function sizeForHumans(int $size, ?int $precision = 2): string
    {
        return Number::fileSize($size, $precision);
    }
}
