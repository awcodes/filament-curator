<?php

namespace FilamentCurator\Resources\MediaResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use FilamentCurator\Resources\MediaResource;

class CreateMedia extends CreateRecord
{
    public static function getResource(): string
    {
        return config('filament-curator.media_resource');
    }
}
