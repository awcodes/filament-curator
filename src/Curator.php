<?php

namespace Awcodes\Curator;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Session;

class Curator
{
    use EvaluatesClosures;

    protected string|Closure $resourceLabel = 'Media';

    protected string|Closure $pluralResourceLabel = 'Media';

    protected string $navigationIcon = 'heroicon-o-photograph';

    protected bool|Closure|null $tableHasIconActions = false;

    protected bool|Closure|null $tableHasGridLayout = true;

    protected bool|Closure $shouldPreserveFilenames = false;

    protected array|Closure $acceptedFileTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'application/pdf'];

    protected int|Closure $maxWidth = 2000;

    protected int|Closure $minSize = 0;

    protected int|Closure $maxSize = 5000;

    protected string|Closure $diskName = 'public';

    protected string|Closure $directory = 'media';

    protected string|null $pathGenerator = null;

    protected string|Closure $visibility = 'public';

    protected array $cloudDisks = ['s3', 'cloudinary', 'imgix'];

    protected string|Closure|null $imageCropAspectRatio = null;

    protected string|Closure|null $imageResizeTargetHeight = null;

    protected string|Closure|null $imageResizeTargetWidth = null;

    protected array|null $curationPresets = [];

    public function resourceLabel(string|Closure $label): static
    {
        $this->resourceLabel = $label;

        return $this;
    }

    public function pluralResourceLabel(string|Closure $label): static
    {
        $this->pluralResourceLabel = $label;

        return $this;
    }

    public function navigationIcon(string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function tableHasIconActions(bool|Closure|null $condition = false): static
    {
        $this->tableHasIconActions = $condition;

        return $this;
    }

    public function tableHasGridLayout(bool|Closure|null $condition = true): static
    {
        $this->tableHasGridLayout = $condition;

        return $this;
    }

    public function curationPresets(array|null $presets): static
    {
        $this->curationPresets = $presets;

        return $this;
    }

    public function preserveFilenames(bool|Closure $condition = true): static
    {
        $this->shouldPreserveFilenames = $condition;

        return $this;
    }

    public function acceptedFileTypes(array|Closure $types): static
    {
        $this->acceptedFileTypes = $types;

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

    public function maxSize(int|Closure $size): static
    {
        $this->maxSize = $size;

        return $this;
    }

    public function disk(string|Closure $disk): static
    {
        $this->diskName = $disk;

        return $this;
    }

    public function directory(Closure|string|null $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function pathGenerator(string|null $generator): static
    {
        $this->pathGenerator = $generator;

        return $this;
    }

    public function visibility(string|Closure $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function cloudDisks(array $disks): static
    {
        $this->cloudDisks = $disks;

        return $this;
    }

    public function imageCropAspectRatio(string|Closure|null $ratio): static
    {
        $this->imageCropAspectRatio = $ratio;

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

    public function getResourceLabel(): string
    {
        return $this->evaluate($this->resourceLabel);
    }

    public function getPluralResourceLabel(): string
    {
        return $this->evaluate($this->pluralResourceLabel);
    }

    public function getNavigationIcon(): string
    {
        return $this->navigationIcon;
    }

    public function getCurationPresets(): array|null
    {
        return collect($this->curationPresets)->map(function($preset) {
            return $preset->getPreset();
        })->toArray();
    }

    public function shouldTableHaveIconActions(): string
    {
        return $this->evaluate($this->tableHasIconActions);
    }

    public function shouldTableHaveGridLayout(): string
    {
        if (! Session::has('tableLayout')) {
            Session::put('tableLayout', $this->evaluate($this->tableHasGridLayout));
        }

        return Session::get('tableLayout');
    }

    public function shouldPreserveFilenames(): bool
    {
        return $this->evaluate($this->shouldPreserveFilenames);
    }

    public function getAcceptedFileTypes(): array
    {
        return $this->evaluate($this->acceptedFileTypes);
    }

    public function getMaxWidth(): int
    {
        return $this->evaluate($this->maxWidth);
    }

    public function getMinSize(): int
    {
        return $this->evaluate($this->minSize);
    }

    public function getMaxSize(): int
    {
        return $this->evaluate($this->maxSize);
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->diskName);
    }

    public function getPathGenerator(): ?string
    {
        return $this->pathGenerator;
    }

    public function getDirectory(): string
    {
        return $this->evaluate($this->directory);
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility);
    }

    public function getCloudDisks(): array
    {
        return $this->cloudDisks;
    }

    public function getImageCropAspectRatio(): ?string
    {
        return $this->evaluate($this->imageCropAspectRatio);
    }

    public function getImageResizeTargetHeight(): ?string
    {
        return $this->evaluate($this->imageResizeTargetHeight);
    }

    public function getImageResizeTargetWidth(): ?string
    {
        return $this->evaluate($this->imageResizeTargetWidth);
    }

    public function isResizable(string $ext): bool
    {
        return in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }
}
