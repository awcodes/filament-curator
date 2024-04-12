<?php

namespace Awcodes\Curator\DTO;

class MediaDTO
{
    public function __construct(
        public string $path,
        public ?string $name = null,
        public ?string $alt = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $caption = null,
        public ?int $width = null,
        public ?int $height = null,
        public ?bool $isResizable = null,
        public ?bool $isPreviewable = null,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

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
