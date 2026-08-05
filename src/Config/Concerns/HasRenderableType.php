<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config\Concerns;

use Awcodes\Curator\Enums\AudioExtensions;
use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Enums\PreviewableExtensions;
use Awcodes\Curator\Enums\VideoExtensions;

trait HasRenderableType
{
    public function isResizable(string $extension): bool
    {
        $extension = mb_strtolower($extension);

        return in_array($extension, PreviewableExtensions::toArray()) && $extension !== PreviewableExtensions::Svg->value;
    }

    public function isPreviewable(string $extension): bool
    {
        return in_array(mb_strtolower($extension), PreviewableExtensions::toArray());
    }

    public function isVideo(string $extension): bool
    {
        return in_array(mb_strtolower($extension), VideoExtensions::toArray());
    }

    public function isDocument(string $extension): bool
    {
        return ! static::isPreviewable($extension) && ! static::isVideo($extension);
    }

    public function isSvg(string $extension): bool
    {
        return mb_strtolower($extension) === PreviewableExtensions::Svg->value;
    }

    /**
     * Whether the extension belongs to a type Curator does not accept by
     * default because it can execute in the browser or on the server.
     */
    public function isRestricted(string $extension): bool
    {
        return in_array(mb_strtolower($extension), MimeType::restrictedExtensions(), true);
    }

    public function isAudio(string $extension): bool
    {
        return in_array(mb_strtolower($extension), AudioExtensions::toArray());
    }
}
