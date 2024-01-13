<?php

namespace Awcodes\Curator\Enums;

enum Resizable: string
{
    case Jpg = 'jpg';
    case Jpeg = 'jpeg';
    case Png = 'png';
    case Webp = 'webp';
    case Bmp = 'bmp';
    case Avif = 'avif';
    case Gif = 'gif';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
