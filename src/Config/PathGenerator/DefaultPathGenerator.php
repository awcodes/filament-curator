<?php

namespace FilamentCurator\Config\PathGenerator;

class DefaultPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        return $baseDir ?? '';
    }
}
