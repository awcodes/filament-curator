<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Enums\CurationFormats;
use Awcodes\Curator\Facades\Curation;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\MediaResource;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Interfaces\ImageInterface;
use Livewire\Attributes\Locked;
use Livewire\Component;
use LogicException;

class CuratorCuration extends Component
{
    /**
     * JPEG's largest side. The crop is trimmed to the image anyway, so this only turns away values no cropper sends.
     */
    protected const MAX_COORDINATE = 65535;

    protected const DEFAULT_MAX_DIMENSION = 8192;

    #[Locked]
    public Media $media;

    public string $modalId;

    public ?array $presets = null;

    public ?array $formats = null;

    public string $statePath;

    public function saveCuration($data = null): void
    {
        $this->authorizeCuration();

        $data = $this->validateCuration($data);

        $storage = Storage::disk($this->media->disk);

        $manager = Glide::getServer()->getApi()->getImageManager();
        $image = $manager->read($storage->get($this->media->path));
        $extension = $data['format'] ?? $this->media->ext;

        // The decoder has already applied the EXIF orientation, so the crop data describes the upright image.
        $image->orient();

        // cropperjs mirrors the image in its own frame and then rotates it, clockwise for a positive angle.
        // Intervention rotates counter-clockwise, so the angle is negated.
        if ($data['scaleX'] < 0) {
            $image->flop();
        }

        if ($data['scaleY'] < 0) {
            $image->flip();
        }

        $image->rotate(-$data['rotate']);

        $box = $this->clampCropBox($data, $image);

        $preset = $this->findPreset($data['key']);

        [$aspectWidth, $aspectHeight] = $this->getOutputSize($preset, $box);

        $image->crop($box['width'], $box['height'], $box['x'], $box['y']);

        if ($preset instanceof CurationPreset) {
            $this->fitToPreset($image, $data, $box, $aspectWidth, $aspectHeight);
        } else {
            $image->resize($aspectWidth, $aspectHeight);
        }

        $encodedImage = $image->encodeByExtension(extension: $extension, quality: $data['quality'] ?? 60);

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
     * Only someone who may edit the media may write curations next to it.
     */
    protected function authorizeCuration(): void
    {
        $resource = App::make(MediaResource::class);

        try {
            $allowed = $resource::can('update', $this->media);
        } catch (LogicException) {
            // Filament's strict authorization mode throws when no policy defines `update`; treat that as a denial.
            $allowed = false;
        }

        abort_unless($allowed, 403);
    }

    /**
     * Intervention pads a crop that reaches past the image, so an unchecked box
     * allocates whatever size the client asks for. The box is trimmed to the
     * image, which after the rotation above is its bounding box, exactly the
     * frame cropperjs measures the crop in.
     *
     * @return array{x: int, y: int, width: int, height: int}
     *
     * @throws ValidationException
     */
    protected function clampCropBox(array $data, ImageInterface $image): array
    {
        $left = max(0, (int) floor((float) $data['x']));
        $top = max(0, (int) floor((float) $data['y']));
        $right = min($image->width(), (int) round((float) $data['x'] + (float) $data['width']));
        $bottom = min($image->height(), (int) round((float) $data['y'] + (float) $data['height']));

        if ($right <= $left || $bottom <= $top) {
            throw ValidationException::withMessages([
                'width' => trans('curator::views.curation.crop_out_of_bounds'),
            ]);
        }

        return ['x' => $left, 'y' => $top, 'width' => $right - $left, 'height' => $bottom - $top];
    }

    protected function findPreset(string $key): ?CurationPreset
    {
        return collect(Curation::getPresets())->first(fn (CurationPreset $preset): bool => $preset->getKey() === $key);
    }

    /**
     * A registered preset is rendered at its own size. Anything else keeps the crop's size in the original image's
     * pixels, so the result doesn't depend on how large the cropper was on screen, scaled down to fit
     * `curator.curation_max_dimension` when it's larger.
     *
     * @param  array{x: int, y: int, width: int, height: int}  $box
     * @return array{0: int, 1: int}
     */
    protected function getOutputSize(?CurationPreset $preset, array $box): array
    {
        if ($preset instanceof CurationPreset) {
            return [$preset->getWidth(), $preset->getHeight()];
        }

        $width = $box['width'];
        $height = $box['height'];
        $limit = (int) config('curator.curation_max_dimension', self::DEFAULT_MAX_DIMENSION);

        if ($limit > 0 && max($width, $height) > $limit) {
            $ratio = $limit / max($width, $height);
            $width = max(1, min($limit, (int) round($width * $ratio)));
            $height = max(1, min($limit, (int) round($height * $ratio)));
        }

        return [$width, $height];
    }

    /**
     * The cropper lets a preset's box overhang a narrow or short image. The box
     * still maps onto the whole preset, so the trimmed part is scaled by the
     * box's own factor and set at its offset on a preset-sized canvas, with the
     * overhang padded white as Intervention pads it. Scaling it to fill the
     * preset instead would stretch it.
     *
     * @param  array{x: int, y: int, width: int, height: int}  $box
     */
    protected function fitToPreset(ImageInterface $image, array $data, array $box, int $width, int $height): void
    {
        $scaleX = $width / (float) $data['width'];
        $scaleY = $height / (float) $data['height'];

        $offsetX = min($width - 1, max(0, (int) round(($box['x'] - (float) $data['x']) * $scaleX)));
        $offsetY = min($height - 1, max(0, (int) round(($box['y'] - (float) $data['y']) * $scaleY)));

        $partWidth = max(1, min($width - $offsetX, (int) round($box['width'] * $scaleX)));
        $partHeight = max(1, min($height - $offsetY, (int) round($box['height'] * $scaleY)));

        $image->resize($partWidth, $partHeight);

        if ($partWidth !== $width || $partHeight !== $height) {
            $image->crop($width, $height, -$offsetX, -$offsetY);
        }
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
            'width' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_COORDINATE],
            'height' => ['required', 'numeric', 'min:1', 'max:' . self::MAX_COORDINATE],
            'x' => ['required', 'numeric', 'min:-' . self::MAX_COORDINATE, 'max:' . self::MAX_COORDINATE],
            'y' => ['required', 'numeric', 'min:-' . self::MAX_COORDINATE, 'max:' . self::MAX_COORDINATE],
            // cropperjs keeps the angle within a single turn and only ever mirrors, never stretches.
            'rotate' => ['required', 'numeric', 'min:-360', 'max:360'],
            'scaleX' => ['required', 'numeric', Rule::in([-1, 1])],
            'scaleY' => ['required', 'numeric', Rule::in([-1, 1])],
            'canvasData' => ['required', 'array'],
            'canvasData.width' => ['required', 'numeric', 'min:1'],
            'canvasData.height' => ['required', 'numeric', 'min:1'],
            'canvasData.naturalWidth' => ['required', 'numeric', 'min:1'],
            'canvasData.naturalHeight' => ['required', 'numeric', 'min:1'],
        ])->validate();
    }
}
