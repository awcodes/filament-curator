<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Config\CuratorConfig;

if (!function_exists('Curator\curator')) {
    function curator(): Curator
    {
        return app(Curator::class);
    }
}

if (!function_exists('Curator\curator_config')) {
    function curator_config(): CuratorConfig
    {
        return app(CuratorConfig::class);
    }
}

if (!function_exists('Curator\is_media_resizable')) {
    function is_media_resizable(string $ext): bool
    {
        return in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }
}
