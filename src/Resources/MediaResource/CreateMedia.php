<?php

namespace Awcodes\Curator\Resources\MediaResource;

use Filament\Resources\Pages\CreateRecord;

class CreateMedia extends CreateRecord
{
    public static function getResource(): string
    {
        return config('curator.media_resource');
    }
}
