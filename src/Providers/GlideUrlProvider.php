<?php

namespace Awcodes\Curator\Providers;

use Awcodes\Curator\Concerns\UrlProvider;
use Awcodes\Curator\Glide\GlideBuilder;

class GlideUrlProvider implements UrlProvider
{
    public static function getThumbnailUrl(string $path): string
    {
        return GlideBuilder::make()->width(200)->height(200)->format('webp')->fit('crop')->toUrl($path);
    }

    public static function getMediumUrl(string $path): string
    {
        return GlideBuilder::make()->width(640)->height(640)->format('webp')->fit('crop')->toUrl($path);
    }

    public static function getLargeUrl(string $path): string
    {
        return GlideBuilder::make()->width(1024)->height(1024)->format('webp')->fit('contain')->toUrl($path);
    }
}