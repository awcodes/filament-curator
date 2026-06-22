<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Concerns\CanGeneratePaths;
use Awcodes\Curator\Concerns\CanNormalizePaths;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
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

            $extension = $file->getClientOriginalExtension();

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

            $disk = Storage::disk($component->getDiskName());

            if ($disk->exists(mb_ltrim($component->getDirectory().'/'.$filename.'.'.$extension, '/'))) {
                $filename = $filename.'-'.time();
            }

            $path = $file->{$storeMethod}(
                $component->getDirectory(),
                $filename.'.'.$extension,
                $component->getDiskName()
            );

            $data = [
                'disk' => $component->getDiskName(),
                'directory' => $component->getDirectory(),
                'visibility' => $component->getVisibility(),
                'name' => $filename,
                'path' => $path,
                'exif' => $exif ?? null,
                'width' => $width ?? null,
                'height' => $height ?? null,
                'size' => $disk->size($path),
                'type' => $disk->mimeType($path),
                'ext' => $extension,
            ];

            if (Config::get('curator.is_tenant_aware') && Filament::hasTenancy()) {
                $data[Config::get('curator.tenant_ownership_relationship_name').'_id'] = Filament::getTenant()->id;
            }

            return $data;
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

    public function saveUploadedFiles(): void
    {
        if (blank($this->getRawState())) {
            $this->rawState([]);

            return;
        }

        if (! is_array($this->getRawState())) {
            $this->rawState([$this->getRawState()]);
        }

        $rawState = array_filter(array_map(function (TemporaryUploadedFile|array $file) {
            if (! $file instanceof TemporaryUploadedFile) {
                return $file;
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
