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

            // SVGs are served as raw markup (they are not routed through Glide),
            // so they are sanitized before anything is written to the disk, and
            // markup the sanitizer can't handle is never stored.
            $svg = null;

            if (Curator::isSvg($extension) || Curator::isSvgMimeType($type)) {
                $svg = $this->sanitizeSvgUpload($file);

                if ($svg === null) {
                    return null;
                }
            }

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

            if (Storage::disk($component->getDiskName())->exists(mb_ltrim($component->getDirectory() . '/' . $filename . '.' . $extension, '/'))) {
                $filename = $filename . '-' . time();
            }

            if ($svg !== null) {
                $path = trim($component->getDirectory() . '/' . $filename . '.' . $extension, '/');

                if (! Storage::disk($component->getDiskName())->put($path, $svg, $component->getVisibility())) {
                    return null;
                }

                $size = strlen($svg);
            } else {
                $size = $file->getSize();

                $path = $file->{$storeMethod}(
                    $component->getDirectory(),
                    $filename . '.' . $extension,
                    $component->getDiskName()
                );
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

                if (blank($acceptedTypes)) {
                    return;
                }

                foreach (Arr::wrap($value) as $file) {
                    if ($file instanceof TemporaryUploadedFile && ! MimeType::isAccepted($this->detectFileType($file), $acceptedTypes)) {
                        $fail(__('validation.mimetypes', [
                            'attribute' => $this->getValidationAttribute(),
                            'values' => implode(', ', $acceptedTypes),
                        ]));

                        return;
                    }
                }
            },
            function (string $attribute, mixed $value, Closure $fail): void {
                foreach (Arr::wrap($value) as $file) {
                    if (
                        $file instanceof TemporaryUploadedFile
                        && Curator::isSvgMimeType($this->detectFileType($file))
                        && $this->sanitizeSvgUpload($file) === null
                    ) {
                        $fail(__('validation.uploaded', ['attribute' => $this->getValidationAttribute()]));

                        return;
                    }
                }
            },
        ];
    }

    /**
     * The sanitized markup of an SVG upload, or null when the sanitizer can't
     * produce any from markup that isn't empty.
     */
    public function sanitizeSvgUpload(TemporaryUploadedFile $file): ?string
    {
        $original = (string) $file->get();
        $clean = Curator::sanitizeSvg($original);

        return $clean === '' && $original !== '' ? null : $clean;
    }

    /**
     * The type of an upload, detected from its own bytes. Filament's
     * `mimetypes` rule uses the upload's getMimeType(), which some Livewire 3
     * and 4 releases take from client-declared storage metadata, so the type
     * that is accepted and stored is checked again here.
     */
    public function detectFileType(TemporaryUploadedFile $file): string
    {
        return MimeType::refineDetectedType(
            MimeType::detectFromStream($file->readStream()),
            $file->getClientOriginalExtension(),
            fn (?int $length = null): string => $length === null ? (string) $file->get() : MimeType::readStream($file->readStream(), $length),
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
}
