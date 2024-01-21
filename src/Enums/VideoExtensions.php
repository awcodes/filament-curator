<?php

namespace Awcodes\Curator\Enums;

enum VideoExtensions: string
{
    case MP4 = 'mp4';
    case WEBM = 'webm';
    case MOV = 'mov';
    case AVI = 'avi';
    case WMV = 'wmv';
    case MPEG = 'mpeg';
    case MPG = 'mpg';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
