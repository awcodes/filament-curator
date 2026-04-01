<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Decoders\BinaryImageDecoder;
use Livewire\Component;

final class CuratorCuration extends Component
{
    public Media $media;

    public string $modalId;

    public null|array $presets = null;

    public null|array $formats = null;

    public string $statePath;

    public function saveCuration($data = null): void
    {
        $storage = Storage::disk($this->media->disk);
        $extension = $data['format'] ?? $this->media->ext;
        $quality = $data['quality'] ?? 60;

        $imageData = base64_decode(explode(',', $data['dataUrl'])[1] ?? '');

        $manager = Glide::getServer()->getApi()->getImageManager();
        $image = $manager->read($imageData, BinaryImageDecoder::class);

        $encodedImage = $image->encodeByExtension(extension: $extension, quality: $quality);

        $curationPath = $this->media->directory . '/' . $this->media->name . '/' . $data['key'] . '.' . $extension;

        $storage->put($curationPath, $encodedImage);

        $curation = [
            'key' => $data['key'],
            'disk' => $this->media->disk,
            'directory' => $this->media->name,
            'visibility' => $this->media->visibility,
            'name' => $data['key'] . '.' . $extension,
            'path' => $curationPath,
            'width' => $data['width'],
            'height' => $data['height'],
            'size' => $storage->size($curationPath),
            'type' => $encodedImage->mediaType(),
            'ext' => $extension,
            'url' => $this->media->visibility === 'private'
                ? $storage->temporaryUrl($curationPath, now()->addMinutes(5))
                : $storage->url($curationPath),
        ];

        $this->dispatch(
            'add-curation',
            statePath: $this->statePath,
            curation: $curation
        );
    }

    public function render(): View
    {
        return view('curator::components.modals.curator-curation', [
            'aspectRatios' => $this->getAspectRatios(),
        ]);
    }

    private function getAspectRatios(): array
    {
        return config(
            key: 'curator.aspect_ratios',
            default: $this->getDefaultAspectRatios()
        );
    }

    private function getDefaultAspectRatios(): array
    {
        return [
            '16:9' => 16 / 9,
            '4:3' => 4 / 3,
            '1:1' => 1,
            '2:3' => 2 / 3,
        ];
    }
}
