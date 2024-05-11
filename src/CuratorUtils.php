<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Facades\Curator;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CuratorUtils
{
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
    ): array {
        $disk = $disk ?? Curator::getDiskName();
        $directory = $directory ?? Curator::getDirectory();
        $storage = Storage::disk($disk);

        if (str_starts_with($path, 'http')) {
            try {
                $fileContents = file_get_contents($path);
            } catch (Exception $e) {
                throw new Exception("Could not download file from {$path}");
            }
        } else {
            $fileContents = file_get_contents($path);
        }

        $ext = (string) Str::of($path)->afterLast('.');

        $filename = Curator::shouldPreserveFilenames()
            ? (string) Str::of(pathinfo($path, PATHINFO_FILENAME))->slug()
            : (string) Str::uuid();

        $filepath = (string) Str::of($directory . '/' . $filename . '.' . $ext)->trim('/');

        if ($storage->exists($filepath)) {
            $filepath = (string) Str::of($directory . '/' . $filename . '-' . time() . '.' . $ext)->trim('/');
        }

        if (! $storage->exists($filepath)) {
            $storage->put($filepath, $fileContents, Curator::getVisibility());
            $fileContents = $storage->get($filepath);
        }

        if (Curator::isResizable($ext)) {
            $image = Image::make($fileContents);
            $image->orientate();
            $width = $image->getWidth();
            $height = $image->getHeight();
            $exif = $image->exif();
        }

        return [
            'disk' => $disk,
            'directory' => $directory,
            'visibility' => Curator::getVisibility(),
            'name' => $filename,
            'path' => $filepath,
            'width' => $width ?? null,
            'height' => $height ?? null,
            'size' => $storage->size($filepath),
            'type' => $storage->mimeType($filepath),
            'ext' => $ext,
            'alt' => $alt ?? null,
            'description' => $description ?? null,
            'caption' => $caption ?? null,
            'exif' => $exif ?? null,
        ];
    }
}
