<?php

namespace FilamentCurator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FilamentCurator\CuratorThumbnails
 */
class CuratorThumbnails extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'curator-thumbnails';
    }
}
