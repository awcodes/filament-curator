<?php

namespace Awcodes\Curator\Glide;

use Awcodes\Curator\CuratorUtils;
use Illuminate\Support\Str;

class GliderFallback
{
    final public function __construct(
        public string $alt,
        public int $height,
        public string $name,
        public string $source,
        public string $type,
        public int $width,
    ) {}

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
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

    public function getAlt(): string
    {
        return $this->alt;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function isResizable(): bool
    {
        return CuratorUtils::isResizable(Str::of($this->getSource())->afterLast('.'));
    }

    public function isPreviewable(): bool
    {
        return CuratorUtils::isResizable(Str::of($this->getSource())->afterLast('.'));
    }
}
