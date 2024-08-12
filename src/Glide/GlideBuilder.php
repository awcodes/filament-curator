<?php

namespace Awcodes\Curator\Glide;

use Illuminate\Support\Str;
use League\Glide\Urls\UrlBuilderFactory;

class GlideBuilder
{
    final public function __construct(
        public ?string $bg = null,
        public ?int $blur = null,
        public ?string $border = null,
        public ?int $bri = null,
        public ?int $con = null,
        public ?string $crop = null,
        public ?int $dpr = null,
        public ?string $filt = null,
        public ?string $fit = null,
        public ?string $flip = null,
        public ?string $fm = null,
        public ?float $gam = null,
        public ?int $h = null,
        public ?int $q = null,
        public string | int | null $or = null,
        public ?int $pixel = null,
        public ?int $sharp = null,
        public ?int $w = null,
        public ?string $mark = null,
        public ?int $markw = null,
        public ?int $markh = null,
        public ?int $markx = null,
        public ?int $marky = null,
        public ?int $markpad = null,
        public ?string $markpos = null,
        public ?int $markalpha = null,
        public ?string $markfit = null,
    ) {}

    public static function make(): static
    {
        return app(static::class);
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn ($value) => $value !== null);
    }

    public function toQueryString(): string
    {
        return http_build_query($this->toArray());
    }

    public function toUrl(string $path): string
    {
        $urlBuilder = UrlBuilderFactory::create((string) Str::of(config('curator.glide.route_path', 'curator'))
            ->trim('/'), config('app.key'));

        return $urlBuilder->getUrl($path, $this->toArray());
    }

    public function background(string $value): static
    {
        $this->bg = $value;

        return $this;
    }

    public function blur(int $value): static
    {
        $this->blur = $value;

        return $this;
    }

    public function border(int $width, string $color, string $method): static
    {
        $this->border = "$width,$color,$method";

        return $this;
    }

    public function brightness(int $value): static
    {
        $this->bri = $value;

        return $this;
    }

    public function contrast(int $value): static
    {
        $this->con = $value;

        return $this;
    }

    public function crop(int $width, int $height, int $x, int $y): static
    {
        $this->crop = "$width,$height,$x,$y";

        return $this;
    }

    public function dpr(int $value): static
    {
        $this->dpr = $value;

        return $this;
    }

    public function filter(string $value): static
    {
        $this->filt = $value;

        return $this;
    }

    public function fit(string $value): static
    {
        $this->fit = $value;

        return $this;
    }

    public function flip(string $value): static
    {
        $this->flip = $value;

        return $this;
    }

    public function format(string $value): static
    {
        $this->fm = $value;

        return $this;
    }

    public function gamma(float $value): static
    {
        $this->gam = $value;

        return $this;
    }

    public function height(int $value): static
    {
        $this->h = $value;

        return $this;
    }

    public function quality(int $value): static
    {
        $this->q = $value;

        return $this;
    }

    public function orientation(string | int $value): static
    {
        $this->or = $value;

        return $this;
    }

    public function pixelate(int $value): static
    {
        $this->pixel = $value;

        return $this;
    }

    public function sharpen(int $value): static
    {
        $this->sharp = $value;

        return $this;
    }

    public function width(int $value): static
    {
        $this->w = $value;

        return $this;
    }

    public function watermarkPath(string $value): static
    {
        $this->mark = $value;

        return $this;
    }

    public function watermarkWidth(int $value): static
    {
        $this->markw = $value;

        return $this;
    }

    public function watermarkHeight(int $value): static
    {
        $this->markh = $value;

        return $this;
    }

    public function watermarkXOffset(int $value): static
    {
        $this->markx = $value;

        return $this;
    }

    public function watermarkYOffset(int $value): static
    {
        $this->marky = $value;

        return $this;
    }

    public function watermarkPadding(int $value): static
    {
        $this->markpad = $value;

        return $this;
    }

    public function watermarkPosition(string $value): static
    {
        $this->markpos = $value;

        return $this;
    }

    public function watermarkAlpha(int $value): static
    {
        $this->markalpha = $value;

        return $this;
    }

    public function watermarkFit(string $value): static
    {
        $this->markfit = $value;

        return $this;
    }
}
