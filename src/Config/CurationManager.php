<?php

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Curations\CurationPreset;

class CurationManager
{
    protected ?array $presets = null;

    public static function configure(): static
    {
        return app(static::class);
    }

    public function presets(array $presets): static
    {
        $this->presets = $presets;

        return $this;
    }

    /**
     * @return array<CurationPreset>
     */
    public function getPresets(): array
    {
        return $this->presets ?? [
            CurationPreset::make('Thumbnail')
                ->height(200)
                ->width(200)
                ->format('webp')
                ->quality(60),
        ];
    }
}