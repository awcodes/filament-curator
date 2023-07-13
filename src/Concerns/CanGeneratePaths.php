<?php

namespace Awcodes\Curator\Concerns;

use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;

trait CanGeneratePaths
{
    protected PathGenerator | string | null $pathGenerator = null;

    public function getPathGenerator(): PathGenerator | string | null
    {
        return $this->pathGenerator ?? config('curator.path_generator');
    }

    public function pathGenerator(PathGenerator | string | null $generator): static
    {
        $this->pathGenerator = $generator;

        return $this;
    }
}
