<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Concerns\CanNormalizePaths;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Generators\PathGenerator;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Livewire\TemporaryUploadedFile;

class Uploader extends FileUpload
{
    use CanNormalizePaths;

    protected string|null $pathGenerator = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file, $state, $set) {
            $filename = $component->shouldPreserveFilenames() ? Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))->slug() : Str::uuid();
            $extension = $file->getClientOriginalExtension();

            $storeMethod = $component->getVisibility() === 'public' ? 'storePubliclyAs' : 'storeAs';

            if (Curator::isResizable($extension)) {
                $image = Image::make($file->getRealPath());
                $width = $image->getWidth();
                $height = $image->getHeight();
            }

            if (Storage::disk($component->getDiskName())->exists(ltrim($component->getDirectory().'/'.$filename.'.'.$extension, '/'))) {
                $filename = $filename.'-'.time();
            }

            $path = $file->{$storeMethod}($component->getDirectory(), $filename.'.'.$extension, $component->getDiskName());

            return [
                'disk' => $component->getDiskName(),
                'directory' => $component->getDirectory(),
                'name' => $filename,
                'path' => $path,
                'width' => $width ?? null,
                'height' => $height ?? null,
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'ext' => $extension,
            ];
        });
    }

    public function pathGenerator(string|null $generator): static
    {
        $this->pathGenerator = $generator;

        return $this;
    }

    public function getDirectory(): ?string
    {
        $directory = $this->directory ?? app('curator')->getDirectory();
        $generator = $this->getPathGenerator() ?? app('curator')->getPathGenerator();

        if (
            class_exists($generator) &&
            (new \ReflectionClass($generator))->implementsInterface(PathGenerator::class)
        ) {
            $path = resolve($generator)->getPath($directory);
        } else {
            $path = $this->evaluate($this->directory);
        }

        return $this->normalizePath($path);
    }

    public function getPathGenerator(): ?string
    {
        return $this->pathGenerator;
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

        $state = array_map(function (TemporaryUploadedFile|array $file) {
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
