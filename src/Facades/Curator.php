<?php

namespace Awcodes\Curator\Facades;

use Awcodes\Curator\Generators\PathGenerator;
use Closure;
use Illuminate\Support\Facades\Facade;
use League\Glide\Server;
use League\Glide\ServerFactory;

/**
 * @method static static disableResourceRegistration()
 * @method static static resourceLabel(string|Closure $label)
 * @method static static pluralResourceLabel(string|Closure $label)
 * @method static static navigationIcon(string $label)
 * @method static static navigationSort(int $order)
 * @method static static navigationGroup(string $group)
 * @method static static registerNavigation(bool|Closure|null $condition)
 * @method static static tableHasIconActions(bool|Closure|null $condition)
 * @method static static tableHasGridLayout(bool|Closure|null $condition)
 * @method static static curationPresets(array|null $presets)
 * @method static static preserveFilenames(bool|Closure $condition)
 * @method static static acceptedFileTypes(array|Closure $types)
 * @method static static maxWidth(int|Closure $width)
 * @method static static minSize(int|Closure $size)
 * @method static static maxSize(int|Closure $size)
 * @method static static disk(string|Closure $disk)
 * @method static static directory(Closure|string|null $directory)
 * @method static static limitToDirectory(bool|Closure|null $condition = false)
 * @method static static pathGenerator(PathGenerator|string|null $generator)
 * @method static static visibility(string|Closure $visibility)
 * @method static static cloudDisks(array $disks)
 * @method static static imageCropAspectRatio(string|Closure|null $ratio)
 * @method static static imageResizeTargetHeight(string|Closure|null $height)
 * @method static static imageResizeTargetWidth(string|Closure|null $width)
 * @method static static glideSourcePathPrefix(string $prefix)
 * @method static static glideCachePathPrefix(string $prefix)
 * @method static static glideServer(Server|ServerFactory|null $server)
 * @method static static glideMaxImageSize(int $size)
 * @method static static glideDriver(string $driver)
 * @method static static gliderFallbacks(array|null $fallbacks)
 * @method static static mediaModel(string $model)
 * @method static bool shouldRegisterResources()
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
 * @method static PathGenerator|string|null getPathGenerator()
 * @method static string getDiskName()
 * @method static string getDirectory()
 * @method static bool isLimitedToDirectory()
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
