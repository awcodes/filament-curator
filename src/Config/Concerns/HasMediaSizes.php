<?php

namespace Awcodes\Curator\Config\Concerns;

use Awcodes\Curator\Glide\GlideBuilder;
use Illuminate\Support\Number;

trait HasMediaSizes
{
    public function getThumbnailUrl(string $path): string
    {
        return GlideBuilder::make()->width(200)->height(200)->format('webp')->fit('crop')->toUrl($path);
    }

    public function getMediumUrl(string $path): string
    {
        return GlideBuilder::make()->width(640)->height(640)->format('webp')->fit('crop')->toUrl($path);
    }

    public function getLargeUrl(string $path): string
    {
        return GlideBuilder::make()->width(1024)->height(1024)->format('webp')->fit('contain')->toUrl($path);
    }

    public function sizeForHumans(int $size, ?int $precision = 2): string
    {
        return Number::fileSize($size, $precision);
    }
}
