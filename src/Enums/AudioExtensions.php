<?php

declare(strict_types=1);

namespace Awcodes\Curator\Enums;

enum AudioExtensions: string
{
    case MP3 = 'mp3';
    case WAV = 'wav';
    case OGG = 'ogg';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
