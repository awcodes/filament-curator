<?php

declare(strict_types=1);

namespace Awcodes\Curator\Glide;

use Awcodes\Curator\Facades\Curator;
use Illuminate\Support\Str;

class GliderFallback
{
    public ?string $alt = null;

    public ?int $height = null;

    public ?string $name = null;

    public ?string $source = null;

    public ?string $type = null;

    public ?int $width = null;

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
        return Curator::isResizable(Str::of($this->getSource())->afterLast('.')->toString());
    }

    public function isPreviewable(): bool
    {
        return Curator::isResizable(Str::of($this->getSource())->afterLast('.')->toString());
    }
}
