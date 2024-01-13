<?php

namespace Awcodes\Curator\View\Components;

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\CuratorUtils;
use Awcodes\Curator\DTO\MediaDTO;
use Awcodes\Curator\Facades\Curator;
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
        public int | Media | string $media,
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
        if (is_string($media)) {
            $this->handleString($media);
        }

        if (is_int($media)) {
            $this->handleInt($media);
        }

        if (is_a($media, Media::class)) {
            $this->handleMedia($media);
        }

        if (! $this->mediaItem) {
            throw new Exception(message: 'Invalid media item provided to Glider component.');
        }
    }

    public function handleString(string $media): void
    {
        $extension = (string) Str::of($media)->afterLast('.');

        $this->mediaItem = new MediaDTO(
            path: $media,
            isResizable: CuratorUtils::isResizable($extension),
            isPreviewable: CuratorUtils::isPreviewable($extension),
        );
    }

    public function handleInt(int $media): void
    {
        $media = Curator::getModel()->where('id', $media)->first();

        if (! $this->media && $this->fallback) {
            $fallback = app(GlideManager::class)->getGliderFallback($this->fallback);
            $dto = new MediaDTO(
                path: $fallback->getSource(),
                alt: $fallback->getAlt(),
                width: $fallback->getWidth(),
                height: $fallback->getHeight(),
                isResizable: $fallback->isResizable(),
                isPreviewable: $fallback->isPreviewable(),
            );
        } else {
            $dto = new MediaDTO(
                path: $media->path,
                alt: $media->alt,
                title: $media->title,
                description: $media->description,
                caption: $media->caption,
                width: $media->width,
                height: $media->height,
                isResizable: $media->is_resizable,
                isPreviewable: $media->is_previewable,
            );
        }

        $this->mediaItem = $dto;
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
            isResizable: $media->is_resizable,
            isPreviewable: $media->is_previewable,
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
        if ($this->srcset) {
            foreach ($this->srcset as $s) {
                $width = preg_replace("/\D/", '', $s);

                if ($this->height === 'auto') {
                    $height = null;
                } else {
                    $height = floor($width * ($this->media->height / $this->media->width));
                }

                $srcset .= $this->buildGlideSource(['w' => $width, 'h' => $height]) . ' ' . $s . ', ';
            }

            return Str::of($srcset)->rtrim(', ');
        }

        return null;
    }

    public function render(): View | Closure | string
    {
        $this->source = $this->buildGlideSource();

        if ($this->srcset) {
            $this->sourceSet = $this->buildSrcSet();
        }

        return function (array $data) {
            return 'curator::components.glider';
        };
    }
}
