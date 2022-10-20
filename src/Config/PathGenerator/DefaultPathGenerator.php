<?php

namespace FilamentCurator\Config\PathGenerator;

class DefaultPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir): string
    {
        return $baseDir ?? '';
    }
}
