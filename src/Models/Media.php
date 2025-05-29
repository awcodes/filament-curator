<?php

namespace Awcodes\Curator\Models;

use Awcodes\Curator\Concerns\HasPackageFactory;
use Awcodes\Curator\Support\Helpers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Glide\Urls\UrlBuilderFactory;

use function Awcodes\Curator\is_media_resizable;

/**
 * @property int $id
 * @property string $disk
 * @property string $directory
 * @property string $visibility
 * @property string $name
 * @property string $path
 * @property int $width
 * @property int $height
 * @property int $size
 * @property string $type
 * @property string $ext
 * @property string $alt
 * @property string $title
 * @property string $description
 * @property string $caption
 * @property array $exif
 * @property array $curations
 * @property string $url
 * @property string $thumbnail_url
 * @property string $medium_url
 * @property string $large_url
 * @property bool $resizable
 * @property string $size_for_humans
 * @property string $pretty_name
 */
class Media extends Model
{
    use HasPackageFactory;

    protected $guarded = [];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'integer',
        'curations' => 'array',
    ];

    protected $appends = [
        'url',
        'thumbnail_url',
        'medium_url',
        'large_url',
        'resizable',
        'size_for_humans',
        'pretty_name',
    ];

    /**
     * A prefix to identify base64 encoded strings in exif data
     */
    protected const BASE64_PREFIX = 'encoded@base64:';

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                return Helpers::getUrl(disk: $this->disk, path: $this->path);
            },
        );
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getSignedUrl(['w' => 200, 'h' => 200, 'fit' => 'crop', 'fm' => 'webp']),
        );
    }

    protected function mediumUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getSignedUrl(['w' => 640, 'h' => 640, 'fit' => 'crop', 'fm' => 'webp']),
        );
    }

    protected function largeUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getSignedUrl(['w' => 1024, 'h' => 1024, 'fit' => 'contain', 'fm' => 'webp']),
        );
    }

    protected function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk($this->disk)->path($this->path),
        );
    }

    protected function resizable(): Attribute
    {
        return Attribute::make(
            get: fn () => is_media_resizable($this->type),
        );
    }

    protected function sizeForHumans(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getSizeForHumans()
        );
    }

    protected function prettyName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getPrettyName()
        );
    }

    /**
     * Cast exif data safely, dealing with any non-utf8 characters
     */
    protected function exif(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($this->decodeExif($value), true),
            set: fn ($value) => json_encode($this->encodeExif($value)),
        );
    }

    public function getPrettyName(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        return $this->name . '.' . $this->ext;
    }

    public function getSizeForHumans(int $precision = 1): string
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
        $size = $this->size;
        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    public function getSignedUrl(array $params = [], bool $force = false): string
    {
        if (! $force) {
            if (
                ! $this->resizable ||
                in_array($this->disk, config('curator.cloud_disks')) ||
                ! Storage::disk($this->disk)->exists($this->path)
            ) {
                return $this->url;
            }
        }

        $routeBasePath = Str::of(config('curator.glide.route_path', 'curator'))
            ->trim('/')
            ->prepend('/')
            ->append('/')
            ->toString();

        $urlBuilder = UrlBuilderFactory::create($routeBasePath, config('app.key'));

        return $urlBuilder->getUrl($this->path, $params);
    }

    public function getCuration(string $key): array
    {
        return Arr::first(collect($this->curations)->filter(function ($item) use ($key) {
            return $item['curation']['key'] === $key;
        }))['curation'] ?? [];
    }

    public function hasCuration(string $key): bool
    {
        return filled($this->getCuration($key));
    }

    /**
     * Recursively encode exif data safely, dealing with any non-utf8 characters
     */
    protected function encodeExif(mixed $value): mixed
    {
        if (! $value) {
            return null;
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->encodeExif($item), $value);
        }

        return $this->needsBase64($value) ? self::BASE64_PREFIX . base64_encode($value) : $value;
    }

    /**
     * Decode exif data safely, dealing with any base64 encoded strings
     */
    protected function decodeExif(mixed $value): mixed
    {
        if (! $value) {
            return null;
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->decodeExif($item), $value);
        }

        if (str_starts_with($value, self::BASE64_PREFIX)) {
            return base64_decode(substr($value, strlen(self::BASE64_PREFIX)));
        }

        return $value;
    }

    /**
     * Detect strings that should be encoded as base64
     */
    protected function needsBase64(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        // check for invalid encoding (likely binary)
        if (! mb_detect_encoding($value, 'UTF-8', true)) {
            return true;
        }

        // encode as base64 if there are any non-printable characters
        $nonPrintable = preg_replace('/[[:print:]\s]/u', '', $value);

        return strlen($nonPrintable) > strlen($value);
    }
}
