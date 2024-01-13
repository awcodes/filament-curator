<?php

namespace Awcodes\Curator\Curations;

use Awcodes\Curator\CuratorUtils;
use Illuminate\Support\Str;

class CurationPreset
{
    final public function __construct(
        public string $key,
        public string $label,
        public int $height,
        public string $format,
        public int $quality,
        public int $width,
    ) {}

    public static function make(string $label): static
    {
        $key = Str::of($label)->slug('_')->toString();

        return app(static::class, [
            'label' => $label,
            'key' => $key,
        ]);
    }

    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function format(string $format): static
    {
        $this->format = $format;

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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
}
