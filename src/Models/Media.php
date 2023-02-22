<?php

namespace Awcodes\Curator\Models;

use Awcodes\Curator\Concerns\HasPackageFactory;
use Awcodes\Curator\Facades\Curator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

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
        'resizable',
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk($this->disk)->url($this->directory.'/'.$this->name.'.'.$this->ext),
        );
    }

    protected function thumbnailUrl(): Attribute
    {
        $urlBuilder = \League\Glide\Urls\UrlBuilderFactory::create('/curator/', config('app.key'));

        return Attribute::make(
            get: fn () => $urlBuilder->getUrl($this->path, ['w' => 200, 'h' => 200, 'fit' => 'crop', 'fm' => 'webp']),
        );
    }

    protected function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk($this->disk)->path($this->directory.'/'.$this->name.'.'.$this->ext),
        );
    }

    protected function resizable(): Attribute
    {
        return Attribute::make(
            get: fn () => Curator::isResizable($this->ext),
        );
    }

    protected function sizeForHumans(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getSizeForHumans()
        );
    }

    public function getSizeForHumans(int $precision = 1): string
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
        $size = $this->size;
        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }

        return round($size, $precision).' '.$units[$i];
    }

    public function getCuration(string $key): array
    {
        return Arr::first(collect($this->curations)->filter(function($item) use ($key) {
            return $item['curation']['key'] === $key;
        }))['curation'] ?? [];
    }
}
