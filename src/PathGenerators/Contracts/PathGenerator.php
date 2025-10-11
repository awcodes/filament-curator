<?php

declare(strict_types=1);

namespace Awcodes\Curator\PathGenerators\Contracts;

interface PathGenerator
{
    public function getPath(?string $baseDir = null): string;
}
