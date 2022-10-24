<?php

namespace FilamentCurator\Forms\Components;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use FilamentCurator\Config\PathGenerator\PathGenerator;
use FilamentCurator\Facades\CuratorThumbnails;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Livewire\TemporaryUploadedFile;

class MediaUpload extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->directory(function () {
            $generator = config('filament-curator.path_generator');
            if (class_exists($generator) && is_subclass_of($generator, PathGenerator::class)) {
                $path = resolve($generator)->getPath(config('filament-curator.directory'));
            } else {
                $path = config('filament-curator.directory');
            }

            // normalization /path//to/dir/ --> path/to/dir
            $path = preg_replace('#/+#', '/', $path);
            if (Str::startsWith($path, '/')) {
                $path = substr($path, 1);
            }
            if (Str::endsWith($path, '/')) {
                $path = substr($path, 0, strlen($path) - 1);
            }

            return $path;
        });

        $this->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file, $state, $set) {
            $filename = $component->shouldPreserveFilenames() ? Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))->slug() : Str::uuid();

            $storeMethod = $component->getVisibility() === 'public' ? 'storePubliclyAs' : 'storeAs';

            if (CuratorThumbnails::isResizable($file->getClientOriginalExtension())) {
                $image = Image::make($file->getRealPath());
                $width = $image->getWidth();
                $height = $image->getHeight();
            }

            if (Storage::disk($component->getDiskName())->exists(ltrim($component->getDirectory() . '/' . $filename . '.' . $file->getClientOriginalExtension(), '/'))) {
                $filename = $filename . '-' . time();
            }

            return [
                'public_id' => ltrim($component->getDirectory() . '/' . $filename, '/'),
                'ext' => $file->getClientOriginalExtension(),
                'type' => $file->getMimeType(),
                'width' => $width ?? null,
                'height' => $height ?? null,
                'disk' => $component->getDiskName(),
                'directory' => $component->getDirectory(),
                'size' => $file->getSize(),
                'filename' => $file->{$storeMethod}($component->getDirectory(), $filename . '.' . $file->getClientOriginalExtension(), $component->getDiskName()),
            ];
        });
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

        if ($this->canReorder && ($callback = $this->reorderUploadedFilesUsing)) {
            $state = $this->evaluate($callback, [
                'state' => $state,
            ]);
        }

        $this->state($state);
    }
}
