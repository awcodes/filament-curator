<?php

declare(strict_types=1);

namespace Awcodes\Curator\Concerns;

use Awcodes\Curator\Facades\Curation;

trait HasCurationPresets
{
    public function getPresets(): ?array
    {
        return collect(Curation::getPresets())->map(function ($preset): array {
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
