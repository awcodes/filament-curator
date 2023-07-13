<?php

namespace Awcodes\Curator\Facades;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection|array getMedia(array|Media|int $ids)
 *
 * @see \Awcodes\Curator\Curator
 */
class Curator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Awcodes\Curator\Curator::class;
    }
}
