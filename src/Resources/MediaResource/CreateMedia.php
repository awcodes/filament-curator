<?php

namespace Awcodes\Curator\Resources\MediaResource;

use Awcodes\Curator\CuratorPlugin;
use Filament\Resources\Pages\CreateRecord;

class CreateMedia extends CreateRecord
{
    public static function getResource(): string
    {
        return CuratorPlugin::get()->getResource();
    }
}
