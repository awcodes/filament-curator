<?php

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Concerns\CanUploadFiles;
use Filament\Support\Concerns\EvaluatesClosures;

class CuratorManager
{
    use CanUploadFiles;
    use EvaluatesClosures;

    public static function configure(): static
    {
        return app(static::class);
    }
}