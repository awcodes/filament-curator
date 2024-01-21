<?php

namespace Awcodes\Curator\Config\Concerns;

use Awcodes\Curator\Enums\PreviewableExtensions;
use Awcodes\Curator\Enums\VideoExtensions;

trait HasRenderableType
{
    public function isResizable(string $extension): bool
    {
        return in_array($extension, PreviewableExtensions::toArray()) && $extension !== PreviewableExtensions::Svg->value;
    }

    public function isPreviewable(string $extension): bool
    {
        return in_array($extension, PreviewableExtensions::toArray());
    }

    public function isVideo(string $extension): bool
    {
        return in_array($extension, VideoExtensions::toArray());
    }

    public function isDocument(string $extension): bool
    {
        return ! (static::isPreviewable($extension) || static::isVideo($extension));
    }
}