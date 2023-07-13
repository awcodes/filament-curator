<?php

namespace Awcodes\Curator\Curations;

abstract class CurationPreset
{
    abstract public function getKey(): string;

    abstract public function getLabel(): string;

    abstract public function getWidth(): int;

    abstract public function getHeight(): int;

    abstract public function getFormat(): string;

    abstract public function getQuality(): int;
}
