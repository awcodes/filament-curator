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
        // The class has no constructor, so container parameters would be
        // discarded. Assign the name after resolving instead.
        $static = app(static::class);
        $static->name = $name;

        return $static;
    }

    // Every value except the name is optional, and these are commonly set from
    // a conditional expression, so null is accepted rather than a TypeError.
    public function alt(?string $alt): static
    {
        $this->alt = $alt;

        return $this;
    }

    public function height(?int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function source(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function type(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function width(?int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function isResizable(): bool
    {
        return Curator::isResizable($this->getExtension());
    }

    public function isPreviewable(): bool
    {
        return Curator::isPreviewable($this->getExtension());
    }

    protected function getExtension(): string
    {
        return Str::of((string) $this->getSource())->afterLast('.')->toString();
    }
}
