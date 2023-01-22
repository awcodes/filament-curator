<?php

namespace Awcodes\Curator\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;

/**
 * @method static resourceLabel(string $label)
 * @method static pluralResourceLabel(string $label)
 * @method static navigationIcon(string $label)
 * @method static tableHasIconActions(bool | Closure | null $condition)
 * @method static tableHasGridLayout(bool | Closure | null $condition)
 * @method static curationPresets(array|null $presets)
 * @method static preserveFilenames(bool | Closure $condition)
 * @method static acceptedFileTypes(array | Closure $types)
 * @method static maxWidth(int | Closure $width)
 * @method static minSize(int | Closure $size)
 * @method static maxSize(int | Closure $size)
 * @method static disk(string | Closure $disk)
 * @method static directory(Closure|string|null $directory)
 * @method static pathGenerator(string|null $generator)
 * @method static visibility(string | Closure $visibility)
 * @method static cloudDisks(array $disks)
 * @method static imageCropAspectRatio(string | Closure | null $ratio)
 * @method static imageResizeTargetHeight(string | Closure | null $height)
 * @method static imageResizeTargetWidth(string | Closure | null $width)
 * @method static string getResourceLabel()
 * @method static string getPluralResourceLabel()
 * @method static string getNavigationIcon()
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
