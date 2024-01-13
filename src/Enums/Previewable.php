<?php

namespace Awcodes\Curator\Enums;

enum Previewable: string
{
    case Jpg = 'jpg';
    case Jpeg = 'jpeg';
    case Png = 'png';
    case Webp = 'webp';
    case Bmp = 'bmp';
    case Avif = 'avif';
    case Gif = 'gif';
    case Svg = 'svg';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
