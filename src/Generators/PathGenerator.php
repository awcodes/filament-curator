<?php

namespace Awcodes\Curator\Generators;

interface PathGenerator
{
    public function getPath(?string $baseDir = null): string;
}
