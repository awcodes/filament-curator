<?php

namespace FilamentCurator\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Actions\Concerns;
use FilamentCurator\Actions\DownloadAction;
use FilamentCurator\Actions\MediaPickerAction;
use FilamentCurator\Config\PathGenerator\PathGenerator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaPicker extends FileUpload
{
    use Concerns\HasColor;
    use Concerns\HasSize;
    use Concerns\CanBeOutlined;

    protected string $view = 'filament-curator::components.media-picker';

    protected string | Htmlable | Closure | null $buttonLabel = null;

    protected string $mediaModel;

    protected bool | Closure | null $fitContent = false;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->buttonLabel = __('filament-curator::media-picker.button_label');
        $this->size = 'md';
        $this->color = 'primary';
        $this->isOutlined = true;

        $this->mediaModel = config('filament-curator.model');
        $this->directory = config('filament-curator.directory');
        $this->preserveFilenames = config('filament-curator.preserve_file_names');
        $this->maxWidth = config('filament-curator.max_width');
        $this->minSize = config('filament-curator.min_size');
        $this->maxSize = config('filament-curator.max_size');
        $this->rules = config('filament-curator.rules');
        $this->acceptedFileTypes = config('filament-curator.accepted_file_types');
        $this->disk = config('filament-curator.disk', 'public');
        $this->visibility = config('filament-curator.visibility', 'public');

        $this->afterStateHydrated(function(FileUpload $component, $state) {
            $component->state($state);
        });

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

    public function getCurrentItem(): Model | null
    {
        return resolve($this->mediaModel)->where('id', $this->getState())->first();
    }

    public function getButtonLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->buttonLabel);
    }

    public function download($state): StreamedResponse
    {
        $item = resolve($this->mediaModel)->where('id', $id)->first();

        return Storage::disk($item['disk'])->download($item['filename']);
    }

    public function getFitContent(): bool
    {
        return $this->evaluate($this->fitContent);
    }

    public function getValidationRules(): array
    {
        $rules = [
            $this->getRequiredValidationRule(),
        ];

        if (filled($count = $this->maxFiles)) {
            $rules[] = "max:{$count}";
        }

        if (filled($count = $this->minFiles)) {
            $rules[] = "min:{$count}";
        }

        $rules[] = function (string $attribute, array|int $value, Closure $fail): void {

            if (!is_array($value)) {
                $value = \Illuminate\Support\Arr::wrap($value);
            }

            $files = array_filter($value, fn (TemporaryUploadedFile | string $file): bool => $file instanceof TemporaryUploadedFile);

            $name = $this->getName();

            $validator = Validator::make(
                data: [$name => $files],
                rules: ["{$name}.*" => array_merge(['file'], parent::getValidationRules())],
                customAttributes: ["{$name}.*" => $this->getValidationAttribute()],
            );

            if (! $validator->fails()) {
                return;
            }

            $fail($validator->errors()->first());
        };

        return $rules;
    }
}
