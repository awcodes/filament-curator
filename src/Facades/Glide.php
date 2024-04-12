<?php

namespace Awcodes\Curator\Facades;

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Glide\GliderFallback;
use Illuminate\Support\Facades\Facade;
use League\Glide\Server;

/**
 * @method static GlideManager configure()
 * @method static GlideManager serverConfig(array $config)
 * @method static GlideManager basePath(string $basePath)
 * @method static GliderFallback | null getGliderFallback(string $name)
 * @method static array getGliderFallbacks()
 * @method static void registerGliderFallback(GliderFallback $fallback)
 * @method static void registerGliderFallbacks(array $fallbacks)
 * @method static Server getServer()
 * @method static string getBasePath()
 * @method static string getToken()
 * @method static string getUrl(string $path, ?array $params = [])
 *
 * @see GlideManager
 */
class Glide extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GlideManager::class;
    }
}
