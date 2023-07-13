<?php

namespace Awcodes\Curator\Glide;

abstract class GliderFallback
{
    abstract public function getAlt(): string;

    abstract public function getHeight(): int;

    abstract public function getKey(): string;

    abstract public function getSource(): string;

    abstract public function getType(): string;

    abstract public function getWidth(): int;
}
