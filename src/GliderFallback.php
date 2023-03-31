<?php

namespace Awcodes\Curator;

class GliderFallback
{
    protected string $key;

    protected string $source;

    protected int $width;

    protected int $height;

    protected string $alt;

    protected string $type = 'image';

    public static function make(string $key): static
    {
        $static = app(static::class, ['key' => $key]);
        $static->key = $key;

        return $static;
    }

    public function alt(string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function source(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getFallback(): array
    {
        return [
            'key' => $this->key,
            'path' => $this->source,
            'alt' => $this->alt,
            'width' => $this->width,
            'height' => $this->height,
            'type' => $this->type,
        ];
    }
}
