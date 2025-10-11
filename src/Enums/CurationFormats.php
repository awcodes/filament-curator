<?php

declare(strict_types=1);

namespace Awcodes\Curator\Enums;

enum CurationFormats: string
{
    case Jpg = 'jpg';
    case Jpeg = 'jpeg';
    case Webp = 'webp';
    case Png = 'png';
    case Avif = 'avif';
}
