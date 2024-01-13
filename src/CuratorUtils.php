<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Enums\Previewable;
use Awcodes\Curator\Enums\Resizable;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CuratorUtils
{
    public static function isResizable(string $extension): bool
    {
        return in_array($extension, Resizable::toArray());
    }

    public static function isPreviewable(string $extension): bool
    {
        return in_array($extension, Previewable::toArray());
    }

    public static function sanitizeExif(array $exif): array
    {
        array_walk_recursive($exif, function (&$entry) {
            if (!mb_detect_encoding($entry, 'utf-8', true)) {
                $entry = utf8_encode($entry);
            }
        });

        return $exif;
    }

    public static function isUsingS3(): bool
    {
        $diskBeforeTestFake = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');

        return config('filesystems.disks.'.strtolower($diskBeforeTestFake).'.driver') === 's3';
    }

    public static function isUsingGCS(): bool
    {
        $diskBeforeTestFake = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');

        return config('filesystems.disks.'.strtolower($diskBeforeTestFake).'.driver') === 'gcs';
    }

    public static function isUsingCloudinary(): bool
    {
        $diskBeforeTestFake = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');

        return config('filesystems.disks.'.strtolower($diskBeforeTestFake).'.driver') === 'cloudinary';
    }

    public static function isUsingCloudDisk(): bool
    {
        return static::isUsingS3() || static::isUsingGCS() || static::isUsingCloudinary();
    }

    /**
     * @throws Exception
     */
    public static function importMedia(
        string $path,
        ?string $disk = null,
        ?string $directory = null,
        ?string $alt = null,
        ?string $caption = null,
        ?string $description = null,
    ): array
    {
        $fileContents = null;
        $curatorManager = app(CuratorManager::class);
        $disk = $disk ?? $curatorManager->getDiskName();
        $directory = $directory ?? $curatorManager->getDirectory();
        $storage = Storage::disk($disk);

        if (str_starts_with($path, 'http')) {
            try {
                $fileContents = file_get_contents($path);
            } catch (Exception $e) {
                throw new Exception("Could not download file from {$path}");
            }
        }

        $ext = (string) Str::of($path)->afterLast('.');

        $filename = $curatorManager->shouldPreserveFilenames()
            ? (string) Str::of(pathinfo($path, PATHINFO_FILENAME))->slug()
            : (string) Str::uuid();

        $filepath = (string) Str::of($directory . '/' . $filename . '.' . $ext)->trim('/');

        if ($storage->exists($filepath)) {
            $filepath = (string) Str::of($directory . '/' . $filename . '-' . time() . '.' . $ext)->trim('/');
        }

        if (! $storage->exists($filepath)) {
            $storage->put($filepath, $fileContents, $curatorManager->getVisibility());
            $fileContents = $storage->get($filepath);
        }

        if (static::isResizable($ext)) {
            $image = Image::make($fileContents);
            $image->orientate();
            $width = $image->getWidth();
            $height = $image->getHeight();
            $exif = $image->exif();
        }

        return [
            'disk' => $disk,
            'directory' => $directory,
            'visibility' => $curatorManager->getVisibility(),
            'name' => $filename,
            'path' => $filepath,
            'width' => $width ?? null,
            'height' => $height ?? null,
            'size' => $storage->size($filepath),
            'mime' => $storage->mimeType($filepath),
            'ext' => $ext,
            'alt' => $alt ?? null,
            'description' => $description ?? null,
            'caption' => $caption ?? null,
            'exif' => $exif ?? null,
        ];
    }
}