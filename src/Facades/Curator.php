<?php

namespace Awcodes\Curator\Facades;

use Awcodes\Curator\Config\CuratorManager;
use Closure;
use Illuminate\Support\Facades\Facade;

/**
 * @method static CuratorManager configure()
 * @method static CuratorManager acceptedFileTypes(array | Closure $types)
 * @method static CuratorManager directory(Closure | string | null $directory)
 * @method static CuratorManager disk(string | Closure $disk)
 * @method static CuratorManager imageCropAspectRatio(string | Closure | null $imageCropAspectRatio)
 * @method static CuratorManager imageResizeMode(string | Closure | null $imageResizeMode)
 * @method static CuratorManager imageResizeTargetHeight(string | Closure | null $imageResizeTargetHeight)
 * @method static CuratorManager imageResizeTargetWidth(string | Closure | null $imageResizeTargetWidth)
 * @method static CuratorManager maxSize(string | Closure | null $maxSize)
 * @method static CuratorManager minSize(string | Closure | null $minSize)
 * @method static CuratorManager preserveFilenames(bool | Closure $shouldPreserveFilenames)
 * @method static CuratorManager visibility(string | Closure | null $visibility)
 * @method static array getAcceptedFileTypes()
 * @method static string getDiskName()
 * @method static string | null getDirectory()
 * @method static string | null getImageCropAspectRatio()
 * @method static string | null getImageResizeMode()
 * @method static string | null getImageResizeTargetHeight()
 * @method static string | null getImageResizeTargetWidth()
 * @method static string | null getMaxSize()
 * @method static string | null getMinSize()
 * @method static string getLargeUrl(string $path)
 * @method static string getMediumUrl(string $path)
 * @method static string getThumbnailUrl(string $path)
 * @method static string | null getVisibility()
 * @method static bool | null shouldPreserveFilenames()
 * @method static bool isResizable(string $ext)
 * @method static bool isPreviewable(string $ext)
 * @method static bool isVideo(string $ext)
 * @method static bool isDocument(string $ext)
 * @method static bool isUsingCloudinary()
 * @method static bool isUsingCloudDisk()
 * @method static bool isUsingGCS()
 * @method static bool isUsingS3()
 * @method static array sanitizeExif(array $exif)
 * @method static string sizeForHumans(int $size, int | null $precision = 2)
 *
 * @see CuratorManager
 */
class Curator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CuratorManager::class;
    }
}
