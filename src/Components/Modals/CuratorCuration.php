<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Enums\CurationFormats;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
        $data = $this->validateCuration($data);

        $storage = Storage::disk($this->media->disk);

        $manager = Glide::getServer()->getApi()->getImageManager();
        $image = $manager->read($storage->get($this->media->path));
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
        $curationPath = $this->media->directory . '/' . $this->media->name . '/' . $data['key'] . '.' . $extension;

        $storage->put($curationPath, $encodedImage);

        $curation = [
            'key' => $data['key'] ?? $aspectWidth . 'x' . $aspectHeight,
            'disk' => $this->media->disk,
            'directory' => $this->media->name,
            'visibility' => $this->media->visibility,
            'name' => ($data['key'] ?? $aspectWidth . 'x' . $aspectHeight) . '.' . $extension,
            'path' => $curationPath,
            'width' => $aspectWidth,
            'height' => $aspectHeight,
            'size' => $storage->size($curationPath),
            'type' => $encodedImage->mediaType(),
            'ext' => $extension,
            'url' => Media::resolveUrl($this->media->disk, $curationPath, $this->media->visibility),
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

    /**
     * The payload is assembled client side, so none of it can be trusted:
     * `key` lands in a storage path, `format` picks the encoder, and the
     * canvas dimensions are divisors. Flysystem only refuses traversal that
     * escapes the disk root, so a key such as `../../other` would still
     * overwrite a sibling file inside it.
     *
     * @throws ValidationException
     */
    protected function validateCuration(mixed $data): array
    {
        return Validator::make(is_array($data) ? $data : [], [
            // Keys are typed by hand for custom curations, so allow spaces and
            // punctuation but require the name to start and end alphanumeric —
            // that rules out path separators, `..` and leading dots.
            'key' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9](?:[A-Za-z0-9 ._-]*[A-Za-z0-9])?$/'],
            'format' => ['nullable', Rule::enum(CurationFormats::class)],
            'quality' => ['nullable', 'integer', 'min:1', 'max:100'],
            'width' => ['required', 'numeric', 'min:1'],
            'height' => ['required', 'numeric', 'min:1'],
            'x' => ['required', 'numeric'],
            'y' => ['required', 'numeric'],
            'rotate' => ['required', 'numeric'],
            'scaleX' => ['required', 'numeric'],
            'scaleY' => ['required', 'numeric'],
            'canvasData' => ['required', 'array'],
            'canvasData.width' => ['required', 'numeric', 'min:1'],
            'canvasData.height' => ['required', 'numeric', 'min:1'],
            'canvasData.naturalWidth' => ['required', 'numeric', 'min:1'],
            'canvasData.naturalHeight' => ['required', 'numeric', 'min:1'],
        ])->validate();
    }
}
