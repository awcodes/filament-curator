<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Concerns\CanGeneratePaths;
use Awcodes\Curator\Concerns\CanNormalizePaths;
use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Closure;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use ReflectionClass;

class Uploader extends FileUpload
{
    use CanGeneratePaths;
    use CanNormalizePaths;

    protected function setUp(): void
    {
        parent::setUp();

        $this->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?array {
            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence) {
                return null;
            }

            $filename = $component->shouldPreserveFilenames()
                ? Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))->slug()
                : (string) Str::uuid();

            // Validation accepts the file on its detected type, and web servers
            // serve it by its extension, so the extension has to follow the
            // detected type rather than the name the client sent.
            $type = $this->detectFileType($file);
            $extension = MimeType::resolveExtension($type, $file->getClientOriginalExtension());

            $storeMethod = $component->getVisibility() === 'public' ? 'storePubliclyAs' : 'storeAs';

            if (Curator::isResizable($extension)) {
                if (Curator::isUsingCloudDisk()) {
                    $content = Storage::disk($component->getDiskName())->get($file->path());
                } else {
                    $content = $file->getRealPath();
                }

                $manager = Glide::getServer()->getApi()->getImageManager();
                $image = $manager->read($content);
                $width = $image->width();
                $height = $image->height();
                $exif = $image->exif()->toArray();
            }

            if (Storage::disk($component->getDiskName())->exists(ltrim($component->getDirectory() . '/' . $filename . '.' . $extension, '/'))) {
                $filename = $filename . '-' . time();
            }

            $size = $file->getSize();

            $path = $file->{$storeMethod}(
                $component->getDirectory(),
                $filename . '.' . $extension,
                $component->getDiskName()
            );

            // SVGs are served as raw markup (they are not routed through Glide),
            // so strip any embedded scripts before they can execute inline.
            if (Curator::isSvg($extension) || Curator::isSvgMimeType($type)) {
                $disk = Storage::disk($component->getDiskName());
                $disk->put($path, Curator::sanitizeSvg($disk->get($path)), $component->getVisibility());
                $size = $disk->size($path);
            }

            return [
                'disk' => $component->getDiskName(),
                'directory' => $component->getDirectory(),
                'visibility' => $component->getVisibility(),
                'name' => $filename,
                'path' => $path,
                'exif' => $exif ?? null,
                'width' => $width ?? null,
                'height' => $height ?? null,
                'size' => $size ?? null,
                'type' => $type,
                'ext' => $extension,
            ];
        });

        $this->dehydrateStateUsing(fn ($component) => $component->getState());
    }

    /**
     * @throws BindingResolutionException
     */
    public function getDirectory(): ?string
    {
        $path = $this->evaluate($this->directory) ?? config('curator.default_directory');
        $generator = $this->getPathGenerator();

        if (
            $generator &&
            class_exists($generator) &&
            (new ReflectionClass($generator))->implementsInterface(PathGenerator::class)
        ) {
            $path = App::make($generator)->getPath($path);
        }

        return $this->normalizePath($path);
    }

    /**
     * Rejects state that is not a fresh upload before Filament's own rules,
     * which expect only uploads or file paths, see it.
     */
    public function getValidationRules(): array
    {
        return [
            'bail',
            function (string $attribute, mixed $value, Closure $fail): void {
                foreach (Arr::wrap($value) as $file) {
                    if (! $file instanceof TemporaryUploadedFile) {
                        $fail(__('filament-forms::validation.tampered_file_path', ['attribute' => $this->getValidationAttribute()]));

                        return;
                    }
                }
            },
            ...parent::getValidationRules(),
            function (string $attribute, mixed $value, Closure $fail): void {
                $acceptedTypes = $this->getAcceptedFileTypes();

                if ($acceptedTypes === null) {
                    return;
                }

                foreach (Arr::wrap($value) as $file) {
                    if ($file instanceof TemporaryUploadedFile && ! $this->isAcceptedFile($file, $acceptedTypes)) {
                        $fail(__('validation.mimetypes', [
                            'attribute' => $this->getValidationAttribute(),
                            'values' => implode(', ', $acceptedTypes),
                        ]));

                        return;
                    }
                }
            },
        ];
    }

    /**
     * Filament checks accepted types against the upload's getMimeType(), which
     * some Livewire 3 and 4 releases take from the type the browser declared.
     * The uploader checks the type detected from the file's bytes instead, in
     * getValidationRules().
     *
     * @param  array<string> | Arrayable | Closure  $types
     */
    public function acceptedFileTypes(array | Arrayable | Closure $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    /**
     * The type of an upload, detected from its own bytes rather than through
     * getMimeType(), and refined where libmagic under-reports a format.
     */
    public function detectFileType(TemporaryUploadedFile $file): string
    {
        return MimeType::refineDetectedType(
            MimeType::detectFromStream($file->readStream()),
            $file->getClientOriginalExtension(),
            fn (): string => (string) $file->get(),
        );
    }

    public function saveUploadedFiles(): void
    {
        if (blank($this->getRawState())) {
            $this->rawState([]);

            return;
        }

        if (! is_array($this->getRawState())) {
            $this->rawState([$this->getRawState()]);
        }

        // The uploader never loads existing files into its state, so anything
        // other than a fresh upload came from the client and is dropped.
        $rawState = array_filter(array_map(function (mixed $file): TemporaryUploadedFile | array | null {
            if (! $file instanceof TemporaryUploadedFile) {
                return null;
            }

            $callback = $this->saveUploadedFileUsing;

            if (! $callback instanceof Closure) {
                $file->delete();

                return $file;
            }

            $storedFile = $this->evaluate($callback, [
                'file' => $file,
            ]);

            if ($storedFile === null) {
                return null;
            }

            $this->storeFileName($storedFile['path'], $file->getClientOriginalName());

            $file->delete();

            return $storedFile;
        }, Arr::wrap($this->getRawState())));

        $this->rawState($rawState);
        $this->callAfterStateUpdated();
    }

    /**
     * Matches the way Laravel's `mimetypes` rule accepts a file, including
     * refusing a PHP extension unless `php` is listed, but on the detected
     * type.
     *
     * @param  array<int, string>  $acceptedTypes
     */
    protected function isAcceptedFile(TemporaryUploadedFile $file, array $acceptedTypes): bool
    {
        $phpExtensions = ['php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar'];

        if (! in_array('php', $acceptedTypes, true) && in_array(mb_strtolower(trim($file->getClientOriginalExtension())), $phpExtensions, true)) {
            return false;
        }

        return MimeType::isAccepted($this->detectFileType($file), $acceptedTypes);
    }
}
