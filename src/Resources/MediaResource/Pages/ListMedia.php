<?php

namespace FilamentCurator\Resources\MediaResource\Pages;

use Filament\Resources\Pages\ListRecords;
use FilamentCurator\Resources\MediaResource;

class ListMedia extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-curator.media_resource');
    }
}
