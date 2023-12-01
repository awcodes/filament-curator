<?php

namespace Awcodes\Curator\Models;

use Awcodes\Curator\Concerns\HasPackageFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use League\Glide\Urls\UrlBuilderFactory;
use function Awcodes\Curator\is_media_resizable;

class Media extends Model
{
    use HasPackageFactory;

    protected $guarded = [];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'integer',
        'curations' => 'array',
        'exif' => 'array',
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

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (Storage::disk($this->disk)->exists($this->path) === false) {
                    return '';
                }

                try {
                    $isPrivate = Storage::disk($this->disk)->getVisibility($this->path) === 'private';
                } catch (\Throwable) {
                    // ACL not supported on Storage Bucket, Laravel only throws exception here so need to be careful.
                    // so we assume it's private
                    $isPrivate = true;
                }

                return $isPrivate ? Storage::disk($this->disk)->temporaryUrl(
                    $this->path,
                    now()->addMinutes(5)
                ) : Storage::disk($this->disk)->url($this->path);
            },
        );
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getSignedUrl(['w' => 200, 'h' => 200, 'fit' => 'crop', 'fm' => 'webp']),
        );
    }

    protected function mediumUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getSignedUrl(['w' => 640, 'h' => 640, 'fit' => 'crop', 'fm' => 'webp']),
        );
    }

    protected function largeUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getSignedUrl(['w' => 1024, 'h' => 1024, 'fit' => 'contain', 'fm' => 'webp']),
        );
    }

    protected function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn() => Storage::disk($this->disk)->path($this->directory . '/' . $this->name . '.' . $this->ext),
        );
    }

    protected function resizable(): Attribute
    {
        return Attribute::make(
            get: fn() => is_media_resizable($this->ext),
        );
    }

    protected function sizeForHumans(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getSizeForHumans()
        );
    }

    protected function prettyName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getPrettyName()
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

        $urlBuilder = UrlBuilderFactory::create('/curator/', config('app.key'));

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
}
