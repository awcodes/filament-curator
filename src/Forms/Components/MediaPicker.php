<?php

namespace FilamentCurator\Forms\Components;

use FilamentCurator\Models\Media;
use Filament\Forms\Components\Field;

class MediaPicker extends Field
{
    protected string $view = 'filament-curator::components.media-picker';

    public function getCurrentItem($state)
    {
        return resolve(config('filament-curator.model'))->where('id', $state)->first();
    }
}
