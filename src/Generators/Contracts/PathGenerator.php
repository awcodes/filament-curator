<?php

namespace Awcodes\Curator\Generators\Contracts;

interface PathGenerator
{
    public function getPath(?string $baseDir = null): string;
}
