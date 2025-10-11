<?php

declare(strict_types=1);

namespace Awcodes\Curator\Concerns;

interface UrlProvider
{
    public static function getThumbnailUrl(string $path): string;

    public static function getMediumUrl(string $path): string;

    public static function getLargeUrl(string $path): string;
}
