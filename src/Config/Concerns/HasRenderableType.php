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
     * Whether the *detected* type is SVG. Filenames are supplied by the client
     * and the extension can disagree with the bytes on disk, so anything that
     * decides whether markup needs sanitizing has to consult this as well as
     * {@see static::isSvg()}.
     */
    public function isSvgMimeType(?string $type): bool
    {
        return mb_strtolower((string) $type) === MimeType::ImageSvgXml->value;
    }

    /**
     * Whether serving a type inline would let it execute script in the
     * application's origin. Used to stop a sniffed content type from rendering
     * as a document when the stored extension never declared it.
     */
    public function isUnsafeInlineMimeType(?string $type): bool
    {
        $type = mb_strtolower((string) $type);

        return $type === MimeType::ImageSvgXml->value
            || in_array($type, MimeType::restricted(), true);
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
