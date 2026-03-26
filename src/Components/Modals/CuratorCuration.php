<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Decoders\BinaryImageDecoder;
use Livewire\Component;

class CuratorCuration extends Component
{
    public Media $media;

    public string $modalId;

    public ?array $presets = null;

    public ?array $formats = null;

    public string $statePath;

    public function saveCuration($data = null): void
    {
        $storage = Storage::disk($this->media->disk);
//        $filePath = $storage->path($this->media->path);
        $file = $storage->get($this->media->path);

        $manager = Glide::getServer()->getApi()->getImageManager();
        $image = $manager->read($file, BinaryImageDecoder::class);
        $extension = $data['format'] ?? $this->media->ext;

        $aspectWidth = floor(($data['canvasData']['width'] / $data['canvasData']['naturalWidth']) * $data['width']);
        $aspectHeight = floor(($data['canvasData']['height'] / $data['canvasData']['naturalHeight']) * $data['height']);

        $image->orient();

        if ($image->exif('Orientation') > 1) {
            $rotateCorrection = match ($image->exif('Orientation')) {
                3, 4 => 180,
                5, 6 => 90,
                7, 8 => 270,
                default => 0
            };

            $image->rotate($rotateCorrection - $data['rotate']);
        } else {
            $image->rotate($data['rotate']);
        }

        if ($data['scaleX'] === -1) {
            $image->flop();
        }

        if ($data['scaleY'] === -1) {
            $image->flip();
        }

        $encodedImage = $image
            ->crop($data['width'], $data['height'], $data['x'], $data['y'])
            ->resize((int) $aspectWidth, (int) $aspectHeight)
            ->encodeByExtension(extension: $extension, quality: $data['quality'] ?? 60);

        // save image to directory base on media
        $curationPath = $this->media->directory.'/'.$this->media->name.'/'.$data['key'].'.'.$extension;

        $storage->put($curationPath, $encodedImage);

        $curation = [
            'key' => $data['key'] ?? $aspectWidth.'x'.$aspectHeight,
            'disk' => $this->media->disk,
            'directory' => $this->media->name,
            'visibility' => $this->media->visibility,
            'name' => ($data['key'] ?? $aspectWidth.'x'.$aspectHeight).'.'.$extension,
            'path' => $curationPath,
            'width' => $aspectWidth,
            'height' => $aspectHeight,
            'size' => $storage->size($curationPath),
            'type' => $encodedImage->mediaType(),
            'ext' => $extension,
            'url' => $storage->url($curationPath),
        ];

        $this->dispatch(
            'add-curation',
            statePath: $this->statePath,
            curation: $curation
        );
    }

    public function render(): View
    {
        return view('curator::components.modals.curator-curation');
    }
}
