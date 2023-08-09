<?php

namespace RocketFirm\Curator\Generators;

class DefaultPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        return $baseDir ?? '';
    }
}
