<?php

namespace Awcodes\Curator\Config\Concerns;

use Awcodes\Curator\Glide\GliderFallback;

trait HasGliderFallbacks
{
    /**
     * @var array<string, GliderFallback>
     */
    protected array $gliderFallbacks = [];

    public function registerGliderFallback(GliderFallback $fallback): void
    {
        $this->gliderFallbacks[$fallback->getName()] = $fallback;
    }

    public function registerGliderFallbacks(array $fallbacks): void
    {
        foreach ($fallbacks as $fallback) {
            $this->registerGliderFallback($fallback);
        }
    }

    public function getGliderFallbacks(): array
    {
        return $this->gliderFallbacks;
    }

    public function getGliderFallback(string $name): ?GliderFallback
    {
        return $this->gliderFallbacks[$name] ?? null;
    }
}
