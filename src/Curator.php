<?php

namespace RocketFirm\Curator;

use RocketFirm\Curator\Generators\PathGenerator;
use RocketFirm\Curator\Models\Media;
use Closure;
use Filament\Facades\Filament;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;

class Curator
{
    use EvaluatesClosures;

    protected array|Closure $acceptedFileTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'application/pdf'];

    protected array $cloudDisks = ['s3', 'cloudinary', 'imgix'];

    protected array|null $curationPresets = [];

    protected string|Closure|null $directory = null;

    protected string|Closure|null $diskName = null;

    protected string $glideCachePathPrefix = '.cache';

    protected int $glideMaxImageSize = 2000 * 2000;

    protected string $glideDriver = 'gd';

    protected array|null $gliderFallbacks = [];

    protected Server|ServerFactory|null $glideServer = null;

    protected string $glideSourcePathPrefix = 'public';

    protected string|Closure|null $imageCropAspectRatio = null;

    protected string|Closure|null $imageResizeMode = null;

    protected string|Closure|null $imageResizeTargetHeight = null;

    protected string|Closure|null $imageResizeTargetWidth = null;

    protected bool $isLimitedToDirectory = false;

    protected int|Closure $maxSize = 5000;

    protected int|Closure $maxWidth = 2000;

    protected string $mediaModel = Media::class;

    protected int|Closure $minSize = 0;

    protected string|null $navigationGroup = null;

    protected string $navigationIcon = 'heroicon-o-photograph';

    protected int|null $navigationSort = null;

    protected PathGenerator|string|null $pathGenerator = null;

    protected string|Closure $pluralResourceLabel = 'Media';

    protected string|Closure $resourceLabel = 'Media';

    protected bool|Closure $shouldPreserveFilenames = false;

    protected bool $shouldRegisterNavigation = true;

    protected bool|Closure|null $tableHasGridLayout = true;

    protected bool|Closure|null $tableHasIconActions = false;

    protected string|Closure|null $visibility = null;

    protected bool $shouldRegisterResources = true;

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

    public function curationPresets(array|null $presets): static
    {
        $this->curationPresets = $presets;

        return $this;
    }

    public function directory(Closure|string|null $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disableResourceRegistration(): static
    {
        $this->shouldRegisterResources = false;

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

    public function getCurationPresets(): array|null
    {
        return collect($this->curationPresets)->map(function ($preset) {
            return $preset->getPreset();
        })->toArray();
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->diskName) ?? config('forms.default_filesystem_disk');
    }

    public function getDirectory(): string
    {
        return $this->evaluate($this->directory) ?? 'media';
    }

    public function getGlideCachePathPrefix(): string
    {
        return $this->glideCachePathPrefix;
    }

    public function getGlideDriver(): string
    {
        return $this->glideDriver;
    }

    public function getGliderFallback(string $key): ?array
    {
        return collect($this->getGliderFallbacks())->where('key', $key)->sole();
    }

    public function getGliderFallbacks(): ?array
    {
        return collect($this->gliderFallbacks)->map(function ($preset) {
            return $preset->getFallback();
        })->toArray();
    }

    public function getGlideMaxImageSize(): int
    {
        return $this->glideMaxImageSize;
    }

    public function getGlideServer(): Server|ServerFactory
    {
        if (! $this->glideServer) {
            return ServerFactory::create([
                'driver' => $this->getGlideDriver(),
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

    public function getGlideSourcePathPrefix(): string
    {
        return $this->glideSourcePathPrefix;
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

    public function getMediaModelResource(): string
    {
        return Filament::getModelResource($this->mediaModel);
    }

    public function getResourceLabel(): string
    {
        return $this->evaluate($this->resourceLabel);
    }

    public function getPluralResourceLabel(): string
    {
        return $this->evaluate($this->pluralResourceLabel);
    }

    public function getNavigationGroup(): string|null
    {
        return $this->navigationGroup;
    }

    public function getNavigationIcon(): string|null
    {
        return $this->navigationIcon;
    }

    public function getNavigationSort(): int|null
    {
        return $this->navigationSort;
    }

    public function getMaxSize(): int
    {
        return $this->evaluate($this->maxSize);
    }

    public function getMaxWidth(): int
    {
        return $this->evaluate($this->maxWidth);
    }

    public function getMedia(array|Media|int $ids): Collection|array
    {
        if ($ids instanceof Media) {
            return [$ids];
        }

        $ids = array_values($ids);

        if (isset($ids[0]['id'])) {
            return $ids;
        }

        if (filled($ids)) {
            return $this->mediaModel::whereIn('id', $ids)
                ->get()
                ->sortBy(function ($model) use ($ids) {
                    return array_search($model->id, $ids);
                });
        }

        return [];
    }

    public function getMediaModel(): string
    {
        return $this->mediaModel;
    }

    public function getMinSize(): int
    {
        return $this->evaluate($this->minSize);
    }

    public function getPathGenerator(): PathGenerator|string|null
    {
        return $this->pathGenerator;
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility) ?? 'public';
    }

    public function glideCachePathPrefix(string $prefix = '.cache'): static
    {
        $this->glideCachePathPrefix = $prefix;

        return $this;
    }

    public function glideDriver(string $driver = 'gd'): static
    {
        $this->glideDriver = $driver;

        return $this;
    }

    public function glideMaxImageSize(int $size): static
    {
        $this->glideMaxImageSize = $size;

        return $this;
    }

    public function gliderFallbacks(array|null $fallbacks): static
    {
        $this->gliderFallbacks = $fallbacks;

        return $this;
    }

    public function glideServer(Server|ServerFactory|null $server): static
    {
        $this->glideServer = $server;

        return $this;
    }

    public function glideSourcePathPrefix(string $prefix = 'public'): static
    {
        $this->glideSourcePathPrefix = $prefix;

        return $this;
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

    public function isLimitedToDirectory(): bool
    {
        return $this->evaluate($this->isLimitedToDirectory);
    }

    public function isResizable(string $ext): bool
    {
        return in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }

    public function limitToDirectory(bool|Closure|null $condition = true): static
    {
        $this->isLimitedToDirectory = $condition;

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

    public function mediaModel(string $model): static
    {
        $this->mediaModel = $model;

        return $this;
    }

    public function minSize(int|Closure $size): static
    {
        $this->minSize = $size;

        return $this;
    }

    public function navigationGroup(string|null $group = null): static
    {
        $this->navigationGroup = $group;

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

    public function pathGenerator(PathGenerator|string|null $generator): static
    {
        $this->pathGenerator = $generator;

        return $this;
    }

    public function pluralResourceLabel(string|Closure $label): static
    {
        $this->pluralResourceLabel = $label;

        return $this;
    }

    public function preserveFilenames(bool|Closure $condition = true): static
    {
        $this->shouldPreserveFilenames = $condition;

        return $this;
    }

    public function preset(string $key): ?array
    {
        return collect($this->getCurationPresets())->where('key', $key)->sole();
    }

    public function registerNavigation(bool|Closure|null $condition = true): static
    {
        $this->shouldRegisterNavigation = $condition;

        return $this;
    }

    public function resourceLabel(string|Closure $label): static
    {
        $this->resourceLabel = $label;

        return $this;
    }

    public function shouldPreserveFilenames(): bool
    {
        return $this->evaluate($this->shouldPreserveFilenames);
    }

    public function shouldRegisterNavigation(): bool
    {
        return $this->evaluate($this->shouldRegisterNavigation);
    }

    public function shouldRegisterResources(): bool
    {
        return $this->evaluate($this->shouldRegisterResources);
    }

    public function shouldTableHaveGridLayout(): string
    {
        if (! Session::has('tableLayout')) {
            Session::put('tableLayout', $this->evaluate($this->tableHasGridLayout));
        }

        return Session::get('tableLayout');
    }

    public function shouldTableHaveIconActions(): string
    {
        return $this->evaluate($this->tableHasIconActions);
    }

    public function tableHasGridLayout(bool|Closure|null $condition = true): static
    {
        $this->tableHasGridLayout = $condition;

        return $this;
    }

    public function tableHasIconActions(bool|Closure|null $condition = false): static
    {
        $this->tableHasIconActions = $condition;

        return $this;
    }

    public function visibility(string|Closure $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }
}
