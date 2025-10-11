<?php

declare(strict_types=1);

namespace Awcodes\Curator\PathGenerators;

use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;

class DefaultPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        return $baseDir ?? '';
    }
}
