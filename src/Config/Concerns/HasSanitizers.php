<?php

namespace Awcodes\Curator\Config\Concerns;

trait HasSanitizers
{
    public function sanitizeExif(array $exif): array
    {
        array_walk_recursive($exif, function (&$entry) {
            if (!mb_detect_encoding($entry, 'utf-8', true)) {
                $entry = utf8_encode($entry);
            }
        });

        return $exif;
    }
}