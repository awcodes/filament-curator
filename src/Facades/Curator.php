<?php

namespace Awcodes\Curator\Facades;

use Awcodes\Curator\Generators\PathGenerator;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;
use League\Glide\Server;
use League\Glide\ServerFactory;

/**
 * @method static static acceptedFileTypes(array|Closure $types)
 * @method static static cloudDisks(array $disks)
 * @method static static curationPresets(array|null $presets)
 * @method static static directory(Closure|string|null $directory)
 * @method static static disableResourceRegistration()
 * @method static static disk(string|Closure|null $disk)
 * @method static array getAcceptedFileTypes()
 * @method static array getCloudDisks()
 * @method static array getCurationPresets()
 * @method static string getDiskName()
 * @method static string getDirectory()
 * @method static string getGlideCachePathPrefix()
 * @method static string getGlideDriver()
 * @method static array|null getGliderFallback(string $key)
 * @method static array|null getGliderFallbacks()
 * @method static int getGlideMaxImageSize()
 * @method static Server|ServerFactory|null getGlideServer()
 * @method static string getGlideSourcePathPrefix()
 * @method static string|null getImageCropAspectRatio()
 * @method static string|null getImageResizeMode()
 * @method static string|null getImageResizeTargetHeight()
 * @method static string|null getImageResizeTargetWidth()
 * @method static string getMediaModelResource()
 * @method static string getResourceLabel()
 * @method static string getPluralResourceLabel()
 * @method static string getNavigationGroup()
 * @method static string getNavigationIcon()
 * @method static int getNavigationSort()
 * @method static int getMaxSize()
 * @method static int getMaxWidth()
 * @method static Collection|array getMedia()
 * @method static string getMediaModel()
 * @method static int getMinSize()
 * @method static PathGenerator|string|null getPathGenerator()
 * @method static string getVisibility()
 * @method static static glideCachePathPrefix(string $prefix)
 * @method static static glideDriver(string $driver)
 * @method static static gliderFallbacks(array|null $fallbacks)
 * @method static static glideMaxImageSize(int $size)
 * @method static static glideServer(Server|ServerFactory|null $server)
 * @method static static glideSourcePathPrefix(string $prefix)
 * @method static static imageCropAspectRatio(string|Closure|null $ratio)
 * @method static static imageResizeMode(string|Closure|null $mode)
 * @method static static imageResizeTargetHeight(string|Closure|null $height)
 * @method static static imageResizeTargetWidth(string|Closure|null $width)
 * @method static bool isLimitedToDirectory()
 * @method static bool isResizable(string $ext)
 * @method static static limitToDirectory(bool|Closure|null $condition = false)
 * @method static static maxSize(int|Closure $size)
 * @method static static maxWidth(int|Closure $width)
 * @method static static mediaModel(string $model)
 * @method static static minSize(int|Closure $size)
 * @method static static navigationGroup(string $group)
 * @method static static navigationIcon(string $label)
 * @method static static navigationSort(int $order)
 * @method static static pathGenerator(PathGenerator|string|null $generator)
 * @method static static pluralResourceLabel(string|Closure $label)
 * @method static static preserveFilenames(bool|Closure $condition)
 * @method static array|null preset(string $key)
 * @method static static registerNavigation(bool|Closure|null $condition)
 * @method static static resourceLabel(string|Closure $label)
 * @method static bool shouldPreserveFilenames()
 * @method static bool shouldRegisterNavigation()
 * @method static bool shouldRegisterResources()
 * @method static string shouldTableHaveGridLayout()
 * @method static string shouldTableHaveIconActions()
 * @method static static tableHasGridLayout(bool|Closure|null $condition)
 * @method static static tableHasIconActions(bool|Closure|null $condition)
 * @method static static visibility(string|Closure|null $visibility)
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
