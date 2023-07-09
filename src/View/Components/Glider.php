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

    public string|null $sourceSet = null;

    public function __construct(
        public int|Media|stdClass|null $media,
        public string|null             $glide = null,
        public array|null              $srcset = null,
        public string|null             $sizes = null,
        public string|null             $background = null,
        public string|null             $blur = null,
        public string|null             $border = null,
        public string|null             $brightness = null,
        public string|null             $contrast = null,
        public string|null             $crop = null,
        public string|null             $devicePixelRatio = null,
        public string|null             $filter = null,
        public string|null             $fit = null,
        public string|null             $flip = null,
        public string|null             $format = null,
        public string|null             $gamma = null,
        public string|null             $height = null,
        public string|null             $quality = null,
        public string|null             $orientation = null,
        public string|null             $pixelate = null,
        public string|null             $sharpen = null,
        public string|null             $width = null,
        public string|null             $watermarkPath = null,
        public string|null             $watermarkWidth = null,
        public string|null             $watermarkHeight = null,
        public string|null             $watermarkXOffset = null,
        public string|null             $watermarkYOffset = null,
        public string|null             $watermarkPadding = null,
        public string|null             $watermarkPosition = null,
        public string|null             $watermarkAlpha = null,
        public string|null             $fallback = null,
    )
    {
        if (!$media instanceof Media) {
            $this->media = config('curator.media_model')::where('id', $media)->first();

            if (!$this->media && $this->fallback) {
                $this->media = (object)$this->getGliderFallback($this->fallback);
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
                $urlBuilder = UrlBuilderFactory::create('/curator/', config('app.key'));
                return $urlBuilder->getUrl($this->media->path, $params);
            }

            return $this->media->getSignedUrl($params);
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
        return collect($this->getGliderFallbacks())->where('key', $key)->sole();
    }

    public function getGliderFallbacks(): ?array
    {
        return collect(config('curator.glider_fallbacks'))
            ->map(fn($preset) => $preset->getFallback())
            ->toArray();
    }

    public function render(): View|Closure|string
    {
        if ($this->glide) {
            $this->source = $this->media->resizable ? '/curator/' . $this->media->path . '?' . $this->glide : $this->media->url;
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
