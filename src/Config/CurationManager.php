<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Curations\CurationPreset;

class CurationManager
{
    protected ?array $presets = null;

    /**
     * Each request starts from a copy of the booted manager, so it gets its own presets to change.
     */
    public function __clone(): void
    {
        if ($this->presets !== null) {
            $this->presets = array_map(fn (mixed $preset): mixed => is_object($preset) ? clone $preset : $preset, $this->presets);
        }
    }

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
