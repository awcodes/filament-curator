<?php

namespace Awcodes\Curator\Config\Concerns;

trait HasCurationPresets
{
    protected array|null $curationPresets = [];

    public function curationPresets(array|null $presets): static
    {
        $this->curationPresets = $presets;

        return $this;
    }

    public function getCurationPresets(): array|null
    {
        return collect($this->curationPresets)->map(function ($preset) {
            return $preset->getPreset();
        })->toArray();
    }

    public function preset(string $key): ?array
    {
        return collect($this->getCurationPresets())->where('key', $key)->sole();
    }
}