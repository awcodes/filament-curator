<?php

namespace Awcodes\Curator\Facades;

use Awcodes\Curator\Config\CurationManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CurationManager configure()
 * @method static CurationManager presets(array $presets)
 * @method static array getPresets()
 *
 * @see CurationManager
 */
class Curation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CurationManager::class;
    }
}
