<?php

namespace FilamentCurator;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CuratorThumbnails
{
    protected array $defaults = [
        'thumbnail' => ['width' => 200, 'height' => 200, 'quality' => 60],
    ];

    private function getPathInfo(string $filename): array
    {
        return pathinfo($filename);
    }

    private function getSizes(): array
    {
        return Arr::exists(config('filament-curator.sizes'), 'thumbnail')
            ? config('filament-curator.sizes')
            : array_merge($this->defaults, config('filament-curator.sizes'));
    }

    public function hasSizes(string $ext): bool
    {
        return $this->getSizes() && $this->isResizable($ext);
    }

    public function isResizable(string $ext): bool
    {
        return in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }

    public function generate(Model $media, bool $usePath = false): void
    {
        if ($this->hasSizes($media->ext)) {
            $path_info = $this->getPathInfo($media->filename);

            foreach ($this->getSizes() as $name => $data) {
                $file = $usePath
                    ? Storage::disk($media->disk)->path($media->filename)
                    : Storage::disk($media->disk)->url($media->filename);

                $image = Image::make($file);

                if ($data['width'] == $data['height']) {
                    $image->fit($data['width']);
                } else {
                    $image->resize($data['width'], $data['height'], function ($constraint) use ($data) {
                        if (!$data['height']) {
                            $constraint->aspectRatio();
                        }
                    });
                }

                $image->encode(null, $data['quality']);

                Storage::disk($media->disk)->put(
                    "{$path_info["dirname"]}/{$path_info["filename"]}-{$name}.{$media->ext}",
                    $image->stream()
                );
            }
        }
    }

    public function destroy(Model $media): void
    {
        if ($this->hasSizes($media->ext)) {
            $path_info = $this->getPathInfo($media->filename);

            $thumbnails = collect(Storage::disk($media->disk)->allFiles())->filter(function($item) use ($path_info) {
                return Str::startsWith($item, $path_info['dirname'] . '/' . $path_info['filename'] . '-');
            });

            foreach ($thumbnails as $thumbnail) {
                Storage::disk($media->disk)->delete($thumbnail);
            }
        }
    }
}