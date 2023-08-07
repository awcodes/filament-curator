<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Concerns\CanGeneratePaths;
use Awcodes\Curator\Concerns\CanNormalizePaths;
use function Awcodes\Curator\is_media_resizable;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Uploader extends FileUpload
{
    use CanNormalizePaths;
    use CanGeneratePaths;

    public function getDirectory(): ?string
    {
        $directory = $this->directory ?? config('curator.directory');
        $generator = $this->getPathGenerator() ?? config('curator.path_generator');

        if (
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

        $state = array_map(function (TemporaryUploadedFile | array $file) {
            if (! $file instanceof TemporaryUploadedFile) {
                return $file;
            }

            $callback = $this->saveUploadedFileUsing;

            if (! $callback) {
                $file->delete();

                return $file;
            }

            $storedFile = $this->evaluate($callback, [
                'file' => $file,
            ]);

            $file->delete();

            return $storedFile;
        }, $this->getState());

        $this->state($state);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?array {
            try {
                if (! $file->exists()) {
                    return null;
                }
            } catch (UnableToCheckFileExistence $exception) {
                return null;
            }

            $filename = $component->shouldPreserveFilenames() ? Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))->slug() : Str::uuid();
            $extension = $file->getClientOriginalExtension();

            $storeMethod = $component->getVisibility() === 'public' ? 'storePubliclyAs' : 'storeAs';

            if (is_media_resizable($extension)) {
                if (in_array($component->getDiskName(), config('curator.cloud_disks'))) {
                    $content = Storage::disk($component->getDiskName())->get($file->path());
                } else {
                    $content = $file->getRealPath();
                }

                $image = Image::make($content);
                $image->orientate();
                $width = $image->getWidth();
                $height = $image->getHeight();
                $exif = $image->exif();
            }

            if ($file->exists()) {
                $filename = $filename . '-' . time();
            }

            $path = $file->{$storeMethod}(
                $component->getDirectory(),
                $filename . '.' . $extension,
                $component->getDiskName()
            );

            return [
                'disk' => $component->getDiskName(),
                'directory' => $component->getDirectory(),
                'visibility' => $component->getVisibility(),
                'name' => $filename,
                'path' => $path,
                'exif' => $exif ?? null,
                'width' => $width ?? null,
                'height' => $height ?? null,
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'ext' => $extension,
            ];
        });
    }
}
