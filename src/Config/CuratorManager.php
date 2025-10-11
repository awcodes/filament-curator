<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config;

use Awcodes\Curator\Concerns\CanUploadFiles;
use Awcodes\Curator\Config\Concerns\HasMediaSizes;
use Awcodes\Curator\Config\Concerns\HasRenderableType;
use Awcodes\Curator\Config\Concerns\HasSanitizers;
use Awcodes\Curator\Config\Concerns\SupportsCloudDisks;
use Filament\Support\Concerns\EvaluatesClosures;

class CuratorManager
{
    use CanUploadFiles;
    use EvaluatesClosures;
    use HasMediaSizes;
    use HasRenderableType;
    use HasSanitizers;
    use SupportsCloudDisks;

    public static function configure(): static
    {
        return app(static::class);
    }
}
