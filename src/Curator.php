<?php

namespace Awcodes\Curator;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Session;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;

class Curator
{
    use EvaluatesClosures;

    protected string|Closure $resourceLabel = 'Media';

    protected string|Closure $pluralResourceLabel = 'Media';

    protected string $navigationIcon = 'heroicon-o-photograph';

    protected string|null $navigationGroup = null;

    protected int|null $navigationSort = null;

    protected bool $shouldRegisterNavigation = true;

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

    protected string $glideSourcePathPrefix = 'public';

    protected string $glideCachePathPrefix = '.cache';

    protected int $glideMaxImageSize = 2000*2000;

    protected Server|ServerFactory|null $glideServer = null;

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

    public function navigationSort(int $order): static
    {
        $this->navigationSort = $order;

        return $this;
    }

    public function navigationGroup(string|null $group = null): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function registerNavigation(bool|Closure|null $condition = true): static
    {
        $this->shouldRegisterNavigation = $condition;

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

    public function glideSourcePathPrefix(string $prefix = 'public'): static
    {
        $this->glideCachePathPrefix = $prefix;

        return $this;
    }

    public function glideCachePathPrefix(string $prefix = '.cache'): static
    {
        $this->glideCachePathPrefix = $prefix;

        return $this;
    }

    public function glideServer(Server|ServerFactory|null $server): static
    {
        $this->glideServer = $server;

        return $this;
    }

    public function glideMaxImageSize(int $size): static
    {
        $this->glideMaxImageSize = $size;

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

    public function getNavigationIcon(): string|null
    {
        return $this->navigationIcon;
    }

    public function getNavigationSort(): int|null
    {
        return $this->navigationSort;
    }

    public function getNavigationGroup(): string|null
    {
        return $this->navigationGroup;
    }

    public function shouldRegisterNavigation(): bool
    {
        return $this->evaluate($this->shouldRegisterNavigation);
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

    public function getGlideSourcePathPrefix(): string
    {
        return $this->glideSourcePathPrefix;
    }

    public function getGlideCachePathPrefix(): string
    {
        return $this->glideCachePathPrefix;
    }

    public function getGlideMaxImageSize(): int
    {
        return $this->glideMaxImageSize;
    }

    public function getGlideServer(): Server|ServerFactory
    {
        if (! $this->glideServer) {
            return ServerFactory::create([
                'response' => new LaravelResponseFactory(app('request')),
                'source' => storage_path('app'),
                'source_path_prefix' => $this->getGlideSourcePathPrefix(),
                'cache' => storage_path('app'),
                'cache_path_prefix' => $this->getGlideCachePathPrefix(),
                'max_image_size' => $this->getGlideMaxImageSize(),
            ]);
        }

        return $this->glideServer;
    }
}
