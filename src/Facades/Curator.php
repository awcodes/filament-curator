<?php

namespace Awcodes\Curator\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use League\Glide\Server;
use League\Glide\ServerFactory;

/**
 * @method static resourceLabel(string|Closure $label)
 * @method static pluralResourceLabel(string|Closure $label)
 * @method static navigationIcon(string $label)
 * @method static navigationSort(int $order)
 * @method static navigationGroup(string $group)
 * @method static registerNavigation(bool|Closure|null $condition)
 * @method static tableHasIconActions(bool|Closure|null $condition)
 * @method static tableHasGridLayout(bool|Closure|null $condition)
 * @method static curationPresets(array|null $presets)
 * @method static preserveFilenames(bool|Closure $condition)
 * @method static acceptedFileTypes(array|Closure $types)
 * @method static maxWidth(int|Closure $width)
 * @method static minSize(int|Closure $size)
 * @method static maxSize(int|Closure $size)
 * @method static disk(string|Closure $disk)
 * @method static directory(Closure|string|null $directory)
 * @method static pathGenerator(string|null $generator)
 * @method static visibility(string|Closure $visibility)
 * @method static cloudDisks(array $disks)
 * @method static imageCropAspectRatio(string|Closure|null $ratio)
 * @method static imageResizeTargetHeight(string|Closure|null $height)
 * @method static imageResizeTargetWidth(string|Closure|null $width)
 * @method static glideSourcePathPrefix(string $prefix)
 * @method static glideCachePathPrefix(string $prefix)
 * @method static glideServer(Server|ServerFactory|null $server)
 * @method static glideMaxImageSize(int $size)
 * @method static glideDriver(string $driver)
 * @method static gliderFallbacks(array|null $fallbacks)
 * @method static mediaModel(string $model)
 * @method static string getResourceLabel()
 * @method static string getPluralResourceLabel()
 * @method static string getNavigationIcon()
 * @method static int getNavigationSort()
 * @method static string getNavigationGroup()
 * @method static bool shouldRegisterNavigation()
 * @method static array getCurationPresets()
 * @method static string shouldTableHaveIconActions()
 * @method static string shouldTableHaveGridLayout()
 * @method static bool shouldPreserveFilenames()
 * @method static array getAcceptedFileTypes()
 * @method static int getMaxWidth()
 * @method static int getMinSize()
 * @method static int getMaxSize()
 * @method static string getDiskName()
 * @method static string getDirectory()
 * @method static string getVisibility()
 * @method static array getCloudDisks()
 * @method static array getImageCropAspectRatio()
 * @method static array getImageResizeTargetHeight()
 * @method static array getImageResizeTargetWidth()
 * @method static bool isResizable(string $ext)
 * @method static string getGlideSourcePathPrefix()
 * @method static string getGlideCachePathPrefix()
 * @method static int getGlideMaxImageSize()
 * @method static string getGlideDriver()
 * @method static Server|ServerFactory|null getGlideServer()
 * @method static string getMediaModel()
 * @method static array|null preset(string $key)
 * @method static array|null getGliderFallbacks()
 * @method static array|null getGliderFallback(string $key)
 *
 * @see \Awcodes\Curator\Curator
 */
class Curator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'curator';
    }
}
