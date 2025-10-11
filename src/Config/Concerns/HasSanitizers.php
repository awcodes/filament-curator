<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config\Concerns;

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
}
