<?php

namespace Awcodes\Curator;

class CurationPreset
{
    protected string $key;

    protected string $name;

    protected string $label;

    protected string $format = 'jpg';

    protected int $height;

    protected int $quality = 60;

    protected int $width;

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->key = $name;

        return $static;
    }

    /**
     * @deprecated Use label() instead
     */
    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function quality(int $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getPreset(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->label ?? $this->name,
            'width' => $this->width,
            'height' => $this->height,
            'format' => $this->format,
            'quality' => $this->quality,
        ];
    }
}
