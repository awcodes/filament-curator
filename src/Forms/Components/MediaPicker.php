<?php

namespace FilamentCurator\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\Components\Field;
use Filament\Support\Actions\Concerns;
use FilamentCurator\Actions\DownloadAction;
use FilamentCurator\Actions\MediaPickerAction;
use FilamentCurator\Config\PathGenerator\PathGenerator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaPicker extends Field
{
    use Concerns\HasColor;
    use Concerns\HasSize;
    use Concerns\CanBeOutlined;

    protected string $view = 'filament-curator::components.media-picker';

    protected string | Htmlable | Closure | null $buttonLabel = null;

    protected string $mediaModel;

    protected string | Closure | null $directory = null;

    protected string | Closure | null $diskName = null;

    protected bool | Closure | null $fitContent = false;

    protected string | Closure | null $imageCropAspectRatio = null;

    protected string | Closure | null $imageResizeTargetHeight = null;

    protected string | Closure | null $imageResizeTargetWidth = null;

    protected bool | Closure $isAvatar = false;

    protected int | Closure | null $maxSize = null;

    protected int | Closure | null $minSize = null;

    protected bool | Closure $shouldPreserveFilenames = false;

    protected string | Closure $visibility = 'public';

    private Arrayable | Closure | array $acceptedFileTypes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buttonLabel = __('filament-curator::media-picker.button_label');
        $this->size = 'md';
        $this->color = 'primary';
        $this->isOutlined = true;

        $this->mediaModel = config('filament-curator.model');
        $this->directory = config('filament-curator.directory');
        $this->shouldPreserveFilenames = config('filament-curator.preserve_file_names');
        $this->maxWidth = config('filament-curator.max_width');
        $this->minSize = config('filament-curator.min_size');
        $this->maxSize = config('filament-curator.max_size');
        $this->rules = config('filament-curator.rules');
        $this->acceptedFileTypes = config('filament-curator.accepted_file_types');
        $this->diskName = config('filament-curator.disk', 'public');
        $this->visibility = config('filament-curator.visibility', 'public');

        $this->registerActions([
            MediaPickerAction::make(),
            DownloadAction::make(),
        ]);
    }

    public function fitContent(bool | Closure | null $fitContent = true): static
    {
        $this->fitContent = $fitContent;

        return $this;
    }

    public function buttonLabel(string | Htmlable | Closure | null $buttonLabel): static
    {
        $this->buttonLabel = $buttonLabel;

        return $this;
    }

    public function directory(Closure | PathGenerator | string | null $directory): static
    {
        if (
            class_exists($directory) &&
            is_subclass_of($directory, PathGenerator::class)
        ) {
            $path = resolve($directory)->getPath(config('filament-curator.directory'));
        } else {
            $path = $directory ?? config('filament-curator.directory');
        }

        // normalization /path//to/dir/ --> path/to/dir
        $path = preg_replace('#/+#', '/', $path);
        if (Str::startsWith($path, '/')) {
            $path = substr($path, 1);
        }
        if (Str::endsWith($path, '/')) {
            $path = substr($path, 0, strlen($path) - 1);
        }

        $this->directory = $path;

        return $this;
    }

    public function download($state): StreamedResponse
    {
        $item = resolve($this->mediaModel)->where('id', $id)->first();

        return Storage::disk($item['disk'])->download($item['filename']);
    }

    public function acceptedFileTypes(array | Arrayable | Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function disk($name): static
    {
        $this->diskName = $name;

        return $this;
    }

    public function maxSize(int | Closure | null $size): static
    {
        $this->maxSize = $size;

        return $this;
    }

    public function minSize(int | Closure | null $size): static
    {
        $this->minSize = $size;

        return $this;
    }

    public function image(): static
    {
        $this->acceptedFileTypes([
            'image/*',
        ]);

        return $this;
    }

    public function imageCropAspectRatio(string | Closure | null $ratio): static
    {
        $this->imageCropAspectRatio = $ratio;

        return $this;
    }

    public function imagePreviewHeight(string | Closure | null $height): static
    {
        $this->imagePreviewHeight = $height;

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

    public function preserveFilenames(bool | Closure $condition = true): static
    {
        $this->shouldPreserveFilenames = $condition;

        return $this;
    }

    public function getCurrentItem(): Model | null
    {
        return resolve($this->mediaModel)->where('id', $this->getState())->first();
    }

    public function getButtonLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->buttonLabel);
    }

    public function getFitContent(): bool
    {
        return $this->evaluate($this->fitContent);
    }

    public function getDirectory(): ?string
    {
        return $this->evaluate($this->directory);
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->diskName) ?? config('forms.default_filesystem_disk');
    }

    public function getMaxSize(): ?int
    {
        return $this->evaluate($this->maxSize);
    }

    public function getMinSize(): ?int
    {
        return $this->evaluate($this->minSize);
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility);
    }

    public function shouldPreserveFilenames(): bool
    {
        return $this->evaluate($this->shouldPreserveFilenames);
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

    public function getAcceptedFileTypes(): ?array
    {
        $types = $this->evaluate($this->acceptedFileTypes);

        if ($types instanceof Arrayable) {
            $types = $types->toArray();
        }

        return $types;
    }
}
