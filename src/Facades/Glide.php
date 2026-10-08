<?php

declare(strict_types=1);

namespace Awcodes\Curator\Facades;

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Glide\GliderFallback;
use Awcodes\Curator\Models\Media;
use Closure;
use DateTimeInterface;
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
 * @method static string getTemporaryUrl(string $path, array $params = [], DateTimeInterface | int | null $expiration = null, ?string $disk = null)
 * @method static string getMediaUrl(Media | array $media, array $params = [])
 * @method static mixed withTemporaryUrls(?string $disk, Closure $callback, DateTimeInterface | int | null $expiration = null)
 * @method static DateTimeInterface getTemporaryUrlExpiration()
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
