<?php

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Concerns\CanUploadFiles;
use Filament\Support\Concerns\EvaluatesClosures;

class CuratorManager
{
    use CanUploadFiles;
    use Concerns\HasMediaSizes;
    use Concerns\HasRenderableType;
    use Concerns\HasSanitizers;
    use Concerns\SupportsCloudDisks;
    use EvaluatesClosures;

    public static function configure(): static
    {
        return app(static::class);
    }
}
