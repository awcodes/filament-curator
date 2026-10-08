<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Concerns\CanGeneratePaths;
use Awcodes\Curator\Concerns\CanNormalizePaths;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Awcodes\Curator\Support\MimeType;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use function Awcodes\Curator\is_media_resizable;
use function Awcodes\Curator\is_media_svg;
use function Awcodes\Curator\sanitize_svg;

class Uploader extends FileUpload
{
    use CanGeneratePaths;
    use CanNormalizePaths;

    public function getDirectory(): ?string
    {
        $directory = $this->directory ?? config('curator.directory');
        $generator = $this->getPathGenerator() ?? config('curator.path_generator');

        if (
            $generator &&
            class_exists($generator) &&
            (new \ReflectionClass($generator))->implementsInterface(PathGenerator::class)
        ) {
            $path = App::make($generator)->getPath($directory);
        } else {
            $path = $this->evaluate($this->directory);
        }

        return $this->normalizePath($path);
    }

    public function saveUploadedFiles(): void
    {
        if (blank($this->getState())) {
            $this->state([]);

            return;
        }

        if (! is_array($this->getState())) {
            $this->state([$this->getState()]);
        }

        // The uploader never loads existing files into its state, so anything
        // other than a fresh upload came from the browser and is dropped.
        $state = array_filter(array_map(function (mixed $file): ?array {
            if (! $file instanceof TemporaryUploadedFile) {
                return null;
            }

            $callback = $this->saveUploadedFileUsing;

            if (! $callback) {
                $file->delete();

                return null;
            }

            $storedFile = $this->evaluate($callback, [
                'file' => $file,
            ]);

            if (! is_array($storedFile)) {
                return null;
            }

            $this->storeFileName($storedFile['path'], $file->getClientOriginalName());

            $file->delete();

            return $storedFile;
        }, $this->getState()));

        $this->state($state);
    }

    /**
     * Rejects state that is not a fresh upload, and uploads whose content is
     * a document or script type the field does not accept, before Filament's
     * own rules see them.
     */
    public function getValidationRules(): array
    {
        return [
            'bail',
            function (string $attribute, mixed $value, Closure $fail): void {
                foreach (Arr::wrap($value) as $file) {
                    if (! $file instanceof TemporaryUploadedFile) {
                        $fail(__('validation.uploaded', ['attribute' => $this->getValidationAttribute()]));

                        return;
                    }

                    $type = $this->detectFileType($file);
                    $acceptedTypes = $this->getAcceptedFileTypes() ?? [];

                    if (
                        (MimeType::isRestricted($type) || is_media_svg($type))
                        && filled($acceptedTypes)
                        && ! MimeType::isAccepted($type, $acceptedTypes)
                    ) {
                        $fail(__('validation.mimetypes', [
                            'attribute' => $this->getValidationAttribute(),
                            'values' => implode(', ', $acceptedTypes),
                        ]));

                        return;
                    }

                    if (is_media_svg($type) && $this->sanitizeSvgUpload($file) === null) {
                        $fail(__('validation.uploaded', ['attribute' => $this->getValidationAttribute()]));

                        return;
                    }
                }
            },
            ...parent::getValidationRules(),
        ];
    }

    /**
     * The type of an upload, detected from its bytes. The type Livewire reports
     * can come from the client, for example the content type of a direct
     * upload to S3, so it is only used when the bytes give nothing to go on.
     */
    public function detectFileType(TemporaryUploadedFile $file): string
    {
        $type = MimeType::detect($file->readStream()) ?? $file->getMimeType() ?: MimeType::OCTET_STREAM;

        return MimeType::refineDetectedType(
            $type,
            $file->getClientOriginalExtension(),
            fn (): string => (string) $file->get(),
            fn (int $length): string => MimeType::readHead($file->readStream(), $length),
        );
    }

    /**
     * The sanitized markup of an SVG upload, or null when it can't be
     * sanitized, in which case the upload is rejected.
     */
    protected function sanitizeSvgUpload(TemporaryUploadedFile $file): ?string
    {
        $original = (string) $file->get();
        $clean = sanitize_svg($original);

        return ($clean === '' && $original !== '') ? null : $clean;
    }

    public function getSuggestedFileName(TemporaryUploadedFile $file): string
    {
        return $this->shouldPreserveFilenames()
            ? Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))->slug()
            : (string) Str::uuid();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->getUploadedFileNameForStorageUsing(function (Uploader $component, TemporaryUploadedFile $file) {
            return $component->getSuggestedFileName($file);
        });

        $this->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?array {
            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence $exception) {
                return null;
            }

            $filename = $component->getUploadedFileNameForStorage($file);

            // Validation accepts the file on its type, and web servers serve it
            // by its extension, so the extension follows the type detected
            // from the file's bytes rather than the name the client sent.
            $type = $component->detectFileType($file);
            $extension = MimeType::resolveExtension($type, $file->getClientOriginalExtension());

            $storeMethod = $component->getVisibility() === 'public' ? 'storePubliclyAs' : 'storeAs';

            if (is_media_resizable($type)) {
                if (in_array(config('livewire.temporary_file_upload.disk'), config('curator.cloud_disks')) && config('livewire.temporary_file_upload.directory') !== null) {
                    $content = $file->get();
                } else {
                    $content = $file->getRealPath();
                }

                $image = Image::make($content);
                $image->orientate();
                $width = $image->getWidth();
                $height = $image->getHeight();
                $exif = $image->exif();
            }

            if (Storage::disk($component->getDiskName())->exists(ltrim($component->getDirectory() . '/' . $filename . '.' . $extension, '/'))) {
                $filename = $filename . '-' . time();
            }

            // SVGs are served as raw markup (they are not routed through Glide),
            // so scripts are stripped before the file reaches the disk. Markup
            // that can't be sanitized is never written.
            if (is_media_svg($type)) {
                $clean = $component->sanitizeSvgUpload($file);
                $disk = Storage::disk($component->getDiskName());
                $path = ltrim($component->getDirectory() . '/' . $filename . '.' . $extension, '/');

                if ($clean === null || ! $disk->put($path, $clean, $component->getVisibility())) {
                    throw ValidationException::withMessages([
                        $component->getStatePath() => __('validation.uploaded', ['attribute' => $component->getValidationAttribute()]),
                    ]);
                }

                $size = $disk->size($path);
            } else {
                $path = $file->{$storeMethod}(
                    $component->getDirectory(),
                    $filename . '.' . $extension,
                    $component->getDiskName()
                );

                $size = $file->getSize();
            }

            $data = [
                'disk' => $component->getDiskName(),
                'directory' => $component->getDirectory(),
                'visibility' => $component->getVisibility(),
                'name' => $filename,
                'path' => $path,
                'exif' => $exif ?? null,
                'width' => $width ?? null,
                'height' => $height ?? null,
                'size' => $size,
                'type' => $type,
                'ext' => $extension,
            ];

            if (config('curator.is_tenant_aware') && Filament::hasTenancy()) {
                $data[config('curator.tenant_ownership_relationship_name') . '_id'] = Filament::getTenant()->id;
            }

            return $data;
        });
    }
}
