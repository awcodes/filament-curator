<?php

namespace Awcodes\Curator\Facades;

use Awcodes\Curator\Generators\Contracts\PathGenerator;
use Awcodes\Curator\Models\Media;
use Closure;
use Illuminate\Support\Facades\Facade;
use League\Glide\Server;
use League\Glide\ServerFactory;

/**
 * @method static static acceptedFileTypes(array|Closure $types)
 * @method static static cloudDisks(array $disks)
 * @method static static curationPresets(array|null $presets)
 * @method static static directory(Closure|string|null $directory)
 * @method static static disk(string|Closure|null $disk)
 * @method static array getAcceptedFileTypes()
 * @method static array getCloudDisks()
 * @method static array getCurationPresets()
 * @method static string getDiskName()
 * @method static string getDirectory()
 * @method static array|null getGliderFallbacks()
 * @method static Server|ServerFactory|null getGlideServer()
 * @method static string|null getImageCropAspectRatio()
 * @method static string|null getImageResizeMode()
 * @method static string|null getImageResizeTargetHeight()
 * @method static string|null getImageResizeTargetWidth()
 * @method static int getMaxSize()
 * @method static int getMaxWidth()
 * @method static int getMinSize()
 * @method static Media getMediaModel()
 * @method static string getMediaModelResource()
 * @method static PathGenerator|string|null getPathGenerator()
 * @method static string getVisibility()
 * @method static static gliderFallbacks(array|null $fallbacks)
 * @method static static glideServer(Server|ServerFactory|null $server)
 * @method static static imageCropAspectRatio(string|Closure|null $ratio)
 * @method static static imageResizeMode(string|Closure|null $mode)
 * @method static static imageResizeTargetHeight(string|Closure|null $height)
 * @method static static imageResizeTargetWidth(string|Closure|null $width)
 * @method static bool isLimitedToDirectory()
 * @method static static limitToDirectory(bool|Closure|null $condition = false)
 * @method static static minSize(int|Closure|null $size)
 * @method static static maxSize(int|Closure|null $size)
 * @method static static maxWidth(int|Closure|null $width)
 * @method static static mediaModel(string $model)
 * @method static static pathGenerator(PathGenerator|string|null $generator)
 * @method static static preserveFilenames(bool|Closure $condition)
 * @method static array|null preset(string $key)
 * @method static bool shouldPreserveFilenames()
 * @method static static visibility(string|Closure|null $visibility)
 *
 * @see \Awcodes\Curator\Config\CuratorConfig
 */
class CuratorConfig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Awcodes\Curator\Config\CuratorConfig::class;
    }
}
