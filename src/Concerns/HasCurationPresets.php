<?php

namespace Awcodes\Curator\Concerns;

trait HasCurationPresets
{
    public function getPresets(): ?array
    {
        return collect(config('curator.curation_presets'))->map(function ($preset) {
            $preset = new $preset;

            return [
                'key' => $preset->getKey(),
                'label' => $preset->getLabel(),
                'width' => $preset->getWidth(),
                'height' => $preset->getHeight(),
                'format' => $preset->getFormat(),
                'quality' => $preset->getQuality(),
            ];
        })->toArray();
    }

    public function getPreset(string $key): ?array
    {
        return collect($this->getPresets())->where('key', $key)->sole();
    }
}
