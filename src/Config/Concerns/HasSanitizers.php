<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config\Concerns;

use enshrined\svgSanitize\Sanitizer;
use Throwable;

trait HasSanitizers
{
    public function sanitizeExif(array $exif): array
    {
        array_walk_recursive($exif, function (&$entry): void {
            if (is_string($entry) && (in_array(mb_detect_encoding($entry, 'utf-8', true), ['', '0'], true) || mb_detect_encoding($entry, 'utf-8', true) === false)) {
                $entry = mb_convert_encoding($entry, 'UTF-8', 'ISO-8859-1');
            }
        });

        return $exif;
    }

    /**
     * Strip scripts, event handlers and remote references from SVG markup so it
     * cannot execute JavaScript when served inline as a top-level document.
     */
    public function sanitizeSvg(string $svg): string
    {
        $sanitizer = new Sanitizer;
        $sanitizer->removeRemoteReferences(true);

        // The sanitizer returns false when the markup cannot be parsed, and
        // throws for some documents it can parse, such as one whose root isn't
        // <svg>. Fail closed with an empty string rather than the untrusted
        // original in both cases.
        try {
            $clean = $sanitizer->sanitize($svg);
        } catch (Throwable) {
            return '';
        }

        return $clean === false ? '' : $clean;
    }
}
