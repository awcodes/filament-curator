<?php

namespace Awcodes\Curator\DTO;

class MediaDTO
{
    public function __construct(
        readonly public string $path,
        readonly public ?string $alt = null,
        readonly public ?string $title = null,
        readonly public ?string $description = null,
        readonly public ?string $caption = null,
        readonly public ?int $width = null,
        readonly public ?int $height = null,
        readonly public ?bool $isResizable = null,
        readonly public ?bool $isPreviewable = null,
    ){}

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isResizable(): bool
    {
        return $this->isResizable ?? false;
    }

    public function isPreviewable(): bool
    {
        return $this->isPreviewable ?? false;
    }
}