<?php

namespace FilamentCurator\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool hasSizes(string $ext)
 * @method static bool isResizable(string $ext)
 * @method static void generate(Model $media, bool $usePath = false)
 * @method static void destroy(Model $media)
 *
 * @see \FilamentCurator\CuratorThumbnails
 */
class CuratorThumbnails extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'curator-thumbnails';
    }
}
