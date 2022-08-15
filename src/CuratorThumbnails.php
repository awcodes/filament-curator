<?php

namespace FilamentCurator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CuratorThumbnails
{
    private function getPathInfo(string $filename): array
    {
        return pathinfo($filename);
    }

    public function hasSizes(string $ext): bool
    {
        return config('filament-curator.sizes') && in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }

    public function generate(Model $media): void
    {
        if ($this->hasSizes($media->ext)) {
            $pathinfo = $this->getPathInfo($media->filename);

            foreach (config('filament-curator.sizes') as $name => $data) {
                $image = Image::make(
                    Storage::disk($media->disk)->url($media->filename)
                );

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
                    "{$pathinfo["dirname"]}/{$pathinfo["filename"]}-{$name}.{$media->ext}",
                    $image->stream()
                );
            }
        }
    }

    public function destroy(Model $media): void
    {
        if ($this->hasSizes($media->ext)) {
            $pathinfo = $this->getPathInfo($media->filename);

            foreach (config('filament-curator.sizes') as $name => $data) {
                Storage::disk($media->disk)->delete($pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $name . '.' . $media->ext);
            }
        }
    }
}