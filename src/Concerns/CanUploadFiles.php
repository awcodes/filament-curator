<?php

namespace Awcodes\Curator\Concerns;

use Closure;

trait CanUploadFiles
{
    protected array | Closure | null $acceptedFileTypes = null;

    protected string | Closure | null $directory = null;

    protected string | Closure | null $diskName = null;

    protected string | Closure | null $imageCropAspectRatio = null;

    protected string | Closure | null $imageResizeMode = null;

    protected string | Closure | null $imageResizeTargetHeight = null;

    protected string | Closure | null $imageResizeTargetWidth = null;

    protected int | Closure | null $maxSize = null;

    protected int | Closure | null $minSize = null;

    protected bool | Closure $shouldPreserveFilenames = false;

    protected string | Closure | null $visibility = null;

    public function acceptedFileTypes(array | Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function directory(Closure | string | null $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk(string | Closure $disk): static
    {
        $this->diskName = $disk;

        return $this;
    }

    public function getAcceptedFileTypes(): array
    {
        return $this->evaluate($this->acceptedFileTypes) ?? config('curator.accepted_file_types');
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->diskName) ?? config('curator.disk');
    }

    public function getDirectory(): string
    {
        return $this->evaluate($this->directory) ?? config('curator.directory');
    }

    public function getImageCropAspectRatio(): ?string
    {
        return $this->evaluate($this->imageCropAspectRatio) ?? config('curator.image_crop_aspect_ratio');
    }

    public function getImageResizeMode(): ?string
    {
        return $this->evaluate($this->imageResizeMode) ?? config('curator.image_resize_mode');
    }

    public function getImageResizeTargetHeight(): ?string
    {
        return $this->evaluate($this->imageResizeTargetHeight) ?? config('curator.image_resize_target_height');
    }

    public function getImageResizeTargetWidth(): ?string
    {
        return $this->evaluate($this->imageResizeTargetWidth) ?? config('curator.image_resize_target_width');
    }

    public function getMaxSize(): ?string
    {
        return $this->evaluate($this->maxSize) ?? config('curator.max_size');
    }

    public function getMinSize(): ?string
    {
        return $this->evaluate($this->minSize) ?? config('curator.min_size');
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility) ?? config('curator.visibility');
    }

    public function imageCropAspectRatio(string | Closure | null $ratio): static
    {
        $this->imageCropAspectRatio = $ratio;

        return $this;
    }

    public function imageResizeMode(string | Closure | null $mode): static
    {
        $this->imageResizeMode = $mode;

        return $this;
    }

    public function imageResizeTargetHeight(string | Closure | null $height): static
    {
        $this->imageResizeTargetHeight = $height;

        return $this;
    }

    public function imageResizeTargetWidth(string | Closure | null $width): static
    {
        $this->imageResizeTargetWidth = $width;

        return $this;
    }

    public function maxSize(int | Closure $size): static
    {
        $this->maxSize = $size;

        return $this;
    }

    public function minSize(int | Closure $size): static
    {
        $this->minSize = $size;

        return $this;
    }

    public function preserveFilenames(bool | Closure $condition = true): static
    {
        $this->shouldPreserveFilenames = $condition;

        return $this;
    }

    public function shouldPreserveFilenames(): bool
    {
        return $this->evaluate($this->shouldPreserveFilenames) ?? config('curator.preserve_filenames');
    }

    public function visibility(string | Closure $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }
}
