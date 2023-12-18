<?php

namespace Awcodes\Curator\View\Components;

use Awcodes\Curator\Models\Media;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use League\Glide\Urls\UrlBuilderFactory;
use stdClass;

class Glider extends Component
{
    public string $source;

    public ?string $sourceSet = null;

    protected ?string $basePath = null;

    public function __construct(
        public int | Media | stdClass | null $media,
        public ?string $glide = null,
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
        public ?string $fallback = null,
        public bool $force = false,
    ) {
        $this->basePath = Str::of(config('curator.glide.route_path', 'curator'))
            ->trim('/')
            ->prepend('/')
            ->append('/')
            ->toString();

        if (! $media instanceof Media) {
            if (! is_null($media)) {
                $this->media = app(Media::class)::where('id', $media)->first();
            }

            if (! $this->media && $this->fallback) {
                $this->media = (object) $this->getGliderFallback($this->fallback);
            }
        }
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

        if ($this->media) {
            if (is_a($this->media, stdClass::class)) {
                if (str_starts_with($this->media->path, 'http')) {
                    return $this->media->path;
                }

                $urlBuilder = UrlBuilderFactory::create($this->basePath, config('app.key'));

                return $urlBuilder->getUrl($this->media->path, $params);
            }

            return $this->media->getSignedUrl($params, $this->force);
        }

        return '';
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

    public function getGliderFallback(string $key): ?array
    {
        return collect(config('curator.glide.fallbacks'))
            ->map(function ($fallback) {
                $fallback = new $fallback;

                return [
                    'alt' => $fallback->getAlt(),
                    'height' => $fallback->getHeight(),
                    'key' => $fallback->getKey(),
                    'path' => $fallback->getSource(),
                    'width' => $fallback->getWidth(),
                    'type' => $fallback->getType(),
                ];
            })
            ->where('key', $key)
            ->first();
    }

    public function render(): View | Closure | string
    {
        if ($this->glide) {
            $this->source = $this->media->resizable ? $this->basePath . $this->media->path . '?' . $this->glide : $this->media->url;
        } else {
            $this->source = $this->buildGlideSource();
        }

        if ($this->srcset) {
            $this->sourceSet = $this->buildSrcSet();
        }

        return function (array $data) {
            return 'curator::components.glider';
        };
    }
}
