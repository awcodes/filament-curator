<?php

namespace Awcodes\Curator\Config\Concerns;

use Closure;

trait HasFileUploadSettings
{
    protected array|Closure $acceptedFileTypes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/svg+xml',
        'application/pdf'
    ];

    protected array $cloudDisks = [
        's3',
        'cloudinary',
        'imgix'
    ];

    protected string|Closure|null $directory = null;

    protected string|Closure|null $diskName = null;

    protected string|Closure|null $imageCropAspectRatio = null;

    protected string|Closure|null $imageResizeMode = null;

    protected string|Closure|null $imageResizeTargetHeight = null;

    protected string|Closure|null $imageResizeTargetWidth = null;

    protected int|Closure $maxSize = 5000;

    protected int|Closure $maxWidth = 2000;

    protected int|Closure $minSize = 0;

    protected bool|Closure $shouldPreserveFilenames = false;

    protected string|Closure|null $visibility = null;

    public function acceptedFileTypes(array|Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function cloudDisks(array $disks): static
    {
        $this->cloudDisks = $disks;

        return $this;
    }

    public function directory(Closure|string|null $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk(string|Closure $disk): static
    {
        $this->diskName = $disk;

        return $this;
    }

    public function getAcceptedFileTypes(): array
    {
        return $this->evaluate($this->acceptedFileTypes);
    }

    public function getCloudDisks(): array
    {
        return $this->cloudDisks;
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->diskName) ?? env('FILAMENT_FILESYSTEM_DISK', 'public');
    }

    public function getDirectory(): string
    {
        return $this->evaluate($this->directory) ?? 'media';
    }

    public function getImageCropAspectRatio(): ?string
    {
        return $this->evaluate($this->imageCropAspectRatio);
    }

    public function getImageResizeMode(): ?string
    {
        return $this->evaluate($this->imageResizeMode);
    }

    public function getImageResizeTargetHeight(): ?string
    {
        return $this->evaluate($this->imageResizeTargetHeight);
    }

    public function getImageResizeTargetWidth(): ?string
    {
        return $this->evaluate($this->imageResizeTargetWidth);
    }

    public function getMaxSize(): int
    {
        return $this->evaluate($this->maxSize);
    }

    public function getMaxWidth(): int
    {
        return $this->evaluate($this->maxWidth);
    }

    public function getMinSize(): int
    {
        return $this->evaluate($this->minSize);
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility) ?? 'public';
    }

    public function imageCropAspectRatio(string|Closure|null $ratio): static
    {
        $this->imageCropAspectRatio = $ratio;

        return $this;
    }

    public function imageResizeMode(string|Closure|null $mode): static
    {
        $this->imageResizeMode = $mode;

        return $this;
    }

    public function imageResizeTargetHeight(string|Closure|null $height): static
    {
        $this->imageResizeTargetHeight = $height;

        return $this;
    }

    public function imageResizeTargetWidth(string|Closure|null $width): static
    {
        $this->imageResizeTargetWidth = $width;

        return $this;
    }

    public function maxSize(int|Closure $size): static
    {
        $this->maxSize = $size;

        return $this;
    }

    public function maxWidth(int|Closure $width): static
    {
        $this->maxWidth = $width;

        return $this;
    }

    public function minSize(int|Closure $size): static
    {
        $this->minSize = $size;

        return $this;
    }

    public function preserveFilenames(bool|Closure $condition = true): static
    {
        $this->shouldPreserveFilenames = $condition;

        return $this;
    }

    public function shouldPreserveFilenames(): bool
    {
        return $this->evaluate($this->shouldPreserveFilenames);
    }

    public function visibility(string|Closure $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }
}