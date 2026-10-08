<?php

declare(strict_types=1);

namespace Awcodes\Curator;

use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

class CuratorUtils
{
    /**
     * @throws Exception
     */
    public static function importMedia(
        string $path,
        ?string $disk = null,
        ?string $directory = null,
        ?string $visibility = null,
        ?string $alt = null,
        ?string $title = null,
        ?string $caption = null,
        ?string $description = null,
    ): array {
        $disk ??= Curator::getDiskName();
        $directory ??= Curator::getDirectory();
        $visibility ??= Curator::getVisibility();
        $storage = Storage::disk($disk);

        if (str_starts_with($path, 'http')) {
            try {
                $fileContents = file_get_contents($path);
            } catch (Exception) {
                throw new Exception("Could not download file from {$path}");
            }
        } else {
            $fileContents = file_get_contents($path);
        }

        if (! is_string($fileContents)) {
            throw new Exception("Could not read file from {$path}");
        }

        $sourcePath = str_starts_with($path, 'http')
            ? (string) (parse_url($path, PHP_URL_PATH) ?: $path)
            : $path;

        // The source name is not authoritative about the content, and the disk
        // serves the stored file by its extension, so the extension follows the
        // type detected from the bytes, as it does for uploads.
        $sourceExtension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $detectedType = MimeType::refineDetectedType(
            (new FinfoMimeTypeDetector)->detectMimeTypeFromBuffer($fileContents) ?: MimeType::ApplicationOctetStream->value,
            $sourceExtension,
            fn (?int $length = null): string => $length === null ? $fileContents : substr($fileContents, 0, $length),
        );
        $ext = MimeType::resolveExtension($detectedType, $sourceExtension);

        $filename = Curator::shouldPreserveFilenames()
            ? (string) Str::of(pathinfo($sourcePath, PATHINFO_FILENAME))->slug()
            : (string) Str::uuid();

        $filepath = (string) Str::of($directory . '/' . $filename . '.' . $ext)->trim('/');

        if ($storage->exists($filepath)) {
            $filepath = (string) Str::of($directory . '/' . $filename . '-' . time() . '.' . $ext)->trim('/');
        }

        $type = $detectedType;

        // Imported files never pass through the uploader, so they get the same
        // treatment here: SVG markup is sanitized before it is written, and
        // markup the sanitizer can't handle is never stored.
        if (Curator::isSvg($ext) || Curator::isSvgMimeType($type)) {
            $clean = Curator::sanitizeSvg($fileContents);

            if ($clean === '' && $fileContents !== '') {
                throw new Exception("Could not sanitize the SVG from {$path}");
            }

            $fileContents = $clean;
        }

        if (! $storage->exists($filepath)) {
            if (! $storage->put($filepath, $fileContents, $visibility)) {
                throw new Exception("Could not store the file from {$path}");
            }

            $fileContents = $storage->get($filepath);
        }

        if (Curator::isResizable($ext)) {
            $manager = Glide::getServer()->getApi()->getImageManager();
            $image = $manager->read($fileContents);
            $width = $image->width();
            $height = $image->height();
            $exif = $image->exif();
        }

        return [
            'disk' => $disk,
            'directory' => $directory,
            'visibility' => $visibility,
            'name' => $filename,
            'path' => $filepath,
            'width' => $width ?? null,
            'height' => $height ?? null,
            'size' => $storage->size($filepath),
            'type' => $type,
            'ext' => $ext,
            'alt' => $alt ?? null,
            'title' => $title ?? null,
            'description' => $description ?? null,
            'caption' => $caption ?? null,
            'exif' => $exif ?? null,
        ];
    }
}
