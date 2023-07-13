<?php

namespace Awcodes\Curator\Generators;

use Awcodes\Curator\Generators\Contracts\PathGenerator;

class DefaultPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        return $baseDir ?? '';
    }
}
