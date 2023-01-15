<?php

namespace Awcodes\Curator;

class CurationPreset
{
    protected string $key;

    protected string $name;

    protected int $width;

    protected int $height;

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->key = $name;

        return $static;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getPreset(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }
}
