<?php

declare(strict_types=1);

namespace Awcodes\Curator\View\Components;

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\DTO\MediaDTO;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Glide\GliderFallback;
use Awcodes\Curator\Models\Media;
use Closure;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Glider extends Component
{
    public string $source;

    public ?string $sourceSet = null;

    public ?MediaDTO $mediaItem = null;

    /**
     * @throws Exception
     */
    public function __construct(
        public int | Media | string | null $media = null,
        public ?string $fallback = null,
        public ?array $srcset = null,
        public ?string $sizes = null,
        public ?string $background = null,
        public ?string $blur = null,
        public ?string $border = null,
        public ?string $brightness = null,
        public ?string $contrast = null,
        public ?string $crop = null,
        public ?string $devicePixelRatio = null,
        public ?string $filter = null,
        public ?string $fit = null,
        public ?string $flip = null,
        public ?string $format = null,
        public ?string $gamma = null,
        public ?string $height = null,
        public ?string $quality = null,
        public ?string $orientation = null,
        public ?string $pixelate = null,
        public ?string $sharpen = null,
        public ?string $width = null,
        public ?string $watermarkPath = null,
        public ?string $watermarkWidth = null,
        public ?string $watermarkHeight = null,
        public ?string $watermarkXOffset = null,
        public ?string $watermarkYOffset = null,
        public ?string $watermarkPadding = null,
        public ?string $watermarkPosition = null,
        public ?string $watermarkAlpha = null,
    ) {
        if (is_a($media, Media::class)) {
            $this->handleMedia($media);
        } elseif (is_int($media) || (is_string($media) && static::isMediaKey($media))) {
            $this->handleId($media);
        } elseif (is_string($media) && filled($media)) {
            $this->handleString($media);
        }

        // A null media item, or an id that no longer resolves, is the case the
        // fallback exists for.
        if (! $this->mediaItem instanceof MediaDTO) {
            $this->handleFallback();
        }

        if (! $this->mediaItem instanceof MediaDTO) {
            throw new Exception(message: 'Invalid media item provided to Glider component.');
        }
    }

    /**
     * A media id reaches the component as a string whenever it comes back out of
     * a json column, a query string or a settings array, so a bare key has to be
     * looked up rather than taken for a path. Curator's own primary keys are
     * either auto-incrementing integers or uuids (see stubs/migration.stub), and
     * a path always carries an extension, so neither shape is ambiguous.
     */
    public static function isMediaKey(string $media): bool
    {
        return ctype_digit($media) || Str::isUuid($media) || Str::isUlid($media);
    }

    public function handleString(string $media): void
    {
        $extension = (string) Str::of($media)->afterLast('.');

        $this->mediaItem = new MediaDTO(
            path: $media,
            isResizable: Curator::isResizable($extension),
            isPreviewable: Curator::isPreviewable($extension),
            ext: $extension,
        );
    }

    public function handleId(int | string $media): void
    {
        $record = app(Media::class)->whereKey($media)->first();

        // Leave mediaItem unset so the constructor can reach for the fallback.
        if (! $record instanceof Media) {
            return;
        }

        $this->handleMedia($record);
    }

    /** @deprecated Use handleId(), which also accepts string keys. */
    public function handleInt(int $media): void
    {
        $this->handleId($media);
    }

    public function handleFallback(): void
    {
        if ($this->fallback === null) {
            return;
        }

        $fallback = app(GlideManager::class)->getGliderFallback($this->fallback);

        if (! $fallback instanceof GliderFallback) {
            return;
        }

        // A registered fallback whose source resolved to null is a
        // configuration mistake, so say which one rather than reporting the
        // media item as invalid.
        if (blank($fallback->getSource())) {
            throw new Exception(message: 'The [' . $this->fallback . '] glider fallback does not have a source.');
        }

        $this->mediaItem = new MediaDTO(
            path: $fallback->getSource(),
            alt: $fallback->getAlt(),
            width: $fallback->getWidth(),
            height: $fallback->getHeight(),
            isResizable: $fallback->isResizable(),
            isPreviewable: $fallback->isPreviewable(),
            ext: $fallback->getType() ?? (string) Str::of($fallback->getSource())->afterLast('.'),
        );
    }

    public function handleMedia(Media $media): void
    {
        $dto = new MediaDTO(
            path: $media->path,
            alt: $media->alt,
            title: $media->title,
            description: $media->description,
            caption: $media->caption,
            width: $media->width,
            height: $media->height,
            isResizable: Curator::isResizable($media->ext),
            isPreviewable: Curator::isPreviewable($media->ext),
            ext: $media->ext,
        );

        $this->mediaItem = $dto;
    }

    public function buildGlideSource(array $overrides = []): string
    {
        $params = array_filter(array_merge(
            [
                'bg' => $this->background,
                'blur' => $this->blur,
                'border' => $this->border,
                'bri' => $this->brightness,
                'con' => $this->contrast,
                'crop' => $this->crop,
                'dpr' => $this->devicePixelRatio,
                'filt' => $this->filter,
                'fit' => $this->fit,
                'flip' => $this->flip,
                'fm' => $this->format,
                'gam' => $this->gamma,
                'h' => $this->height,
                'q' => $this->quality,
                'or' => $this->orientation,
                'pixel' => $this->pixelate,
                'sharp' => $this->sharpen,
                'w' => $this->width,
                'mark' => $this->watermarkPath,
                'markw' => $this->watermarkWidth,
                'markh' => $this->watermarkHeight,
                'markx' => $this->watermarkXOffset,
                'marky' => $this->watermarkYOffset,
                'markpad' => $this->watermarkPadding,
                'markpos' => $this->watermarkPosition,
                'markalpha' => $this->watermarkAlpha,
            ],
            $overrides
        ));

        if (str_starts_with($this->mediaItem->getPath(), 'http')) {
            return $this->mediaItem->getPath();
        }

        return app(GlideManager::class)->getUrl($this->mediaItem->getPath(), $params);
    }

    public function buildSrcSet(): ?string
    {
        $srcset = '';
        if ($this->srcset !== null && $this->srcset !== []) {
            foreach ($this->srcset as $s) {
                $width = preg_replace("/\D/", '', (string) $s);

                $height = $this->height === 'auto' ? null : floor($width * ($this->mediaItem->getHeight() / $this->mediaItem->getWidth()));

                $srcset .= $this->buildGlideSource(['w' => $width, 'h' => $height]) . ' ' . $s . ', ';
            }

            return (string) Str::of($srcset)->rtrim(', ');
        }

        return null;
    }

    public function render(): View | Closure | string
    {
        $this->source = $this->buildGlideSource();

        if ($this->srcset !== null && $this->srcset !== []) {
            $this->sourceSet = $this->buildSrcSet();
        }

        return fn (array $data): string => 'curator::components.glider';
    }
}
