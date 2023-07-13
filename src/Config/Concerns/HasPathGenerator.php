<?php

namespace Awcodes\Curator\Config\Concerns;

use Awcodes\Curator\Generators\Contracts\PathGenerator;

trait HasPathGenerator
{
    protected PathGenerator|string|null $pathGenerator = null;

    public function getPathGenerator(): PathGenerator|string|null
    {
        return $this->pathGenerator;
    }

    public function pathGenerator(PathGenerator|string|null $generator): static
    {
        $this->pathGenerator = $generator;

        return $this;
    }
}