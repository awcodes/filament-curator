<?php

namespace FilamentCurator\Config\PathGenerator;

interface PathGenerator
{
    public function getPath(?string $baseDir): string;
}
