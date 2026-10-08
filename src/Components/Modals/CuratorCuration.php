<?php

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CuratorCuration extends Component
{
    /**
     * JPEG's largest side. The crop is trimmed to the image anyway, so this only turns away values no cropper sends.
     */
    protected const MAX_COORDINATE = 65535;

    protected const DEFAULT_MAX_DIMENSION = 4096;

    #[Locked]
    public Media $media;

    public string $modalId;

    public ?array $presets;

    public ?array $formats;

    public string $statePath;

    public function saveCuration($data = null): void
    {
        $this->authorizeCuration();

        $data = $this->validateCuration($data);

        $image = Image::make(Storage::disk($this->media->disk)->get($this->media->path));
        $extension = $data['format'] ?? $this->media->ext;

        // The crop is saved at the size it was shown at in the cropper.
        $scaleX = $data['canvasData']['width'] / $data['canvasData']['naturalWidth'];
        $scaleY = $data['canvasData']['height'] / $data['canvasData']['naturalHeight'];

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

        $box = $this->clampCropBox($data, $image->width(), $image->height());

        $image->crop($box['width'], $box['height'], $box['x'], $box['y']);

        if ($this->isPreset($data['key'])) {
            // A preset's box keeps its shape, so the part of it past the image is padded.
            [$aspectWidth, $aspectHeight, $scaleX, $scaleY] = $this->getOutputSize((float) $data['width'], (float) $data['height'], $scaleX, $scaleY);

            $this->fitToBox($image, $data, $box, $aspectWidth, $aspectHeight, $scaleX, $scaleY);
        } else {
            [$aspectWidth, $aspectHeight] = $this->getOutputSize($box['width'], $box['height'], $scaleX, $scaleY);

            $image->resize($aspectWidth, $aspectHeight);
        }

        $image->encode($extension, $data['quality'] ?? 60);

        // save image to directory base on media
        $curationPath = $this->media->directory . '/' . $this->media->name . '/' . $data['key'] . '.' . $extension;

        Storage::disk($this->media->disk)->put($curationPath, $image->stream(), [
            'visibility' => $this->media->visibility,
        ]);

        $curation = [
            'key' => $data['key'] ?? $aspectWidth . 'x' . $aspectHeight,
            'disk' => $this->media->disk,
            'directory' => $this->media->name,
            'visibility' => $this->media->visibility,
            'name' => ($data['key'] ?? $aspectWidth . 'x' . $aspectHeight) . '.' . $extension,
            'path' => $curationPath,
            'width' => $aspectWidth,
            'height' => $aspectHeight,
            'size' => $image->filesize(),
            'type' => $image->mime(),
            'ext' => $extension,
            'url' => Storage::disk($this->media->disk)->url($curationPath),
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
     * Only someone who may edit the media may write curations next to it.
     */
    protected function authorizeCuration(): void
    {
        abort_unless(
            is_null(Gate::getPolicyFor($this->media)) || Gate::allows('update', $this->media),
            403,
        );
    }

    /**
     * Intervention pads a crop that reaches past the image, so an unchecked box
     * allocates whatever size the client asks for. The box is trimmed to the
     * image, which after the rotation above is its bounding box, the frame the
     * cropper measures the crop in.
     *
     * @return array{x: int, y: int, width: int, height: int}
     *
     * @throws ValidationException
     */
    protected function clampCropBox(array $data, int $imageWidth, int $imageHeight): array
    {
        $left = max(0, (int) floor((float) $data['x']));
        $top = max(0, (int) floor((float) $data['y']));
        $right = min($imageWidth, (int) round((float) $data['x'] + (float) $data['width']));
        $bottom = min($imageHeight, (int) round((float) $data['y'] + (float) $data['height']));

        if ($right <= $left || $bottom <= $top) {
            throw ValidationException::withMessages([
                'width' => trans('curator::views.curation.crop_out_of_bounds'),
            ]);
        }

        return ['x' => $left, 'y' => $top, 'width' => $right - $left, 'height' => $bottom - $top];
    }

    protected function isPreset(string $key): bool
    {
        return collect(config('curator.curation_presets', []))
            ->contains(fn (string $preset): bool => (new $preset)->getKey() === $key);
    }

    /**
     * A crop is saved at the size it was shown at in the cropper, scaled down
     * to fit `curator.curation_max_dimension` when it's larger. The scales are
     * returned adjusted to match.
     *
     * @return array{0: int, 1: int, 2: float, 3: float}
     */
    protected function getOutputSize(float $width, float $height, float $scaleX, float $scaleY): array
    {
        $limit = (int) config('curator.curation_max_dimension', self::DEFAULT_MAX_DIMENSION);
        $outputWidth = $width * $scaleX;
        $outputHeight = $height * $scaleY;

        if ($limit > 0 && max($outputWidth, $outputHeight) > $limit) {
            $ratio = $limit / max($outputWidth, $outputHeight);
            $scaleX *= $ratio;
            $scaleY *= $ratio;
            $outputWidth = min($limit, $outputWidth * $ratio);
            $outputHeight = min($limit, $outputHeight * $ratio);
        }

        return [max(1, (int) floor($outputWidth)), max(1, (int) floor($outputHeight)), $scaleX, $scaleY];
    }

    /**
     * The cropper lets a preset's box overhang the image. The trimmed part is
     * scaled by the box's own factor and set at its offset on a transparent
     * canvas the size of the whole box, as Intervention padded it before. The
     * canvas is bounded by the output size.
     *
     * @param  array{x: int, y: int, width: int, height: int}  $box
     */
    protected function fitToBox(InterventionImage $image, array $data, array $box, int $width, int $height, float $scaleX, float $scaleY): void
    {
        $offsetX = min($width - 1, max(0, (int) round(($box['x'] - (float) $data['x']) * $scaleX)));
        $offsetY = min($height - 1, max(0, (int) round(($box['y'] - (float) $data['y']) * $scaleY)));

        $partWidth = max(1, min($width - $offsetX, (int) round($box['width'] * $scaleX)));
        $partHeight = max(1, min($height - $offsetY, (int) round($box['height'] * $scaleY)));

        // Within the image, or off by rounding only: the crop fills the output as it always has.
        if ($offsetX === 0 && $offsetY === 0 && $width - $partWidth <= 1 && $height - $partHeight <= 1) {
            $image->resize($width, $height);

            return;
        }

        $image->resize($partWidth, $partHeight);

        $canvas = Image::canvas($width, $height)->insert($image, 'top-left', $offsetX, $offsetY);

        $image->setCore($canvas->getCore());
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
            'format' => ['nullable', Rule::in(config('curator.curation_formats', []))],
            'quality' => ['nullable', 'integer', 'min:1', 'max:100'],
            'width' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_COORDINATE],
            'height' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_COORDINATE],
            'x' => ['required', 'numeric', 'min:-' . self::MAX_COORDINATE, 'max:' . self::MAX_COORDINATE],
            'y' => ['required', 'numeric', 'min:-' . self::MAX_COORDINATE, 'max:' . self::MAX_COORDINATE],
            // cropperjs keeps the angle within a single turn and only ever mirrors, never stretches.
            'rotate' => ['required', 'numeric', 'min:-360', 'max:360'],
            'scaleX' => ['required', 'numeric', Rule::in([-1, 1])],
            'scaleY' => ['required', 'numeric', Rule::in([-1, 1])],
            'canvasData' => ['required', 'array'],
            'canvasData.width' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_COORDINATE],
            'canvasData.height' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_COORDINATE],
            'canvasData.naturalWidth' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_COORDINATE],
            'canvasData.naturalHeight' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_COORDINATE],
        ])->validate();
    }
}
