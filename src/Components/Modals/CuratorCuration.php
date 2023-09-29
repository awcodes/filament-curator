<?php

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Livewire\Component;

class CuratorCuration extends Component
{
    public Media $media;

    public string $modalId;

    public array|null $presets;

    public string $statePath;

    public function saveCuration($data = null): void
    {
        if (in_array($this->media->disk, Curator::getCloudDisks())) {
            $filePath = Storage::disk($this->media->disk)->url($this->media->path);
        } else {
            $filePath = Storage::disk($this->media->disk)->path($this->media->path);
        }

        $image = Image::make($filePath);
        $extension = $data['format'] ?? $image->extension;

        $aspectWidth = floor(($data['canvasData']['width'] / $data['canvasData']['naturalWidth']) * $data['width']);
        $aspectHeight = floor(($data['canvasData']['height'] / $data['canvasData']['naturalHeight']) * $data['height']);

        $image->orientate();

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
            $image->flip('v');
        }

        if ($data['scaleY'] === -1) {
            $image->flip('h');
        }

        $image->crop($data['width'], $data['height'], $data['x'], $data['y'])
            ->resize($aspectWidth, $aspectHeight)
            ->encode($extension, $data['quality'] ?? 60);

        // save image to directory base on media
        $curationPath = $this->media->directory.'/'.$this->media->name.'/'.$data['key'].'.'.$extension;

        Storage::disk($this->media->disk)->put($curationPath, $image->stream());

        $curation = [
            'key' => $data['key'] ?? $aspectWidth.'x'.$aspectHeight,
            'disk' => $this->media->disk,
            'directory' => $this->media->name,
            'name' => ($data['key'] ?? $aspectWidth.'x'.$aspectHeight).'.'.$extension,
            'path' => $curationPath,
            'width' => $aspectWidth,
            'height' => $aspectHeight,
            'size' => $image->filesize(),
            'type' => $image->mime(),
            'ext' => $extension,
            'url' => Storage::disk($this->media->disk)->url($curationPath),
        ];

        $this->dispatchBrowserEvent('add-curation', ['curation' => $curation, 'statePath' => $this->statePath]);
    }

    public function render(): View
    {
        return view('curator::components.modals.curator-curation');
    }
}
