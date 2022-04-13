<?php

namespace FilamentCurator\Forms\Components;

use FilamentCurator\Models\Media;
use Filament\Forms\Components\Field;

class MediaPicker extends Field
{
    protected string $view = 'filament-curator::components.media-picker';

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (MediaPicker $component, Media $media, $state): void {
            $item = $media->where('id', $state)->first();
            if ($item instanceof Media) {
                $component->state($item);
            }
        });

        $this->dehydrateStateUsing(function ($state): ?int {
            return isset($state['id']) ? $state['id'] : null;
        });
    }
}
