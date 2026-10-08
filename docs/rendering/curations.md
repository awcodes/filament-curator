---
title: Curations
description: Create custom crops per image, and render them with the curation component.
---

# Curations

A curation is a crop saved against one image under a named preset. Where Glide applies the same rule to every image, a curation lets an author decide how one particular image should be cropped.

![The Curator curation modal: an image with a 320 by 180 crop box, and an Adjustments sidebar with the Post thumbnail preset selected, its key, format, quality, position and size, aspect ratio, zoom, flip and crop controls](../assets/curation-light.png#gh-light-mode-only)
![The Curator curation modal: an image with a 320 by 180 crop box, and an Adjustments sidebar with the Post thumbnail preset selected, its key, format, quality, position and size, aspect ratio, zoom, flip and crop controls](../assets/curation-dark.png#gh-dark-mode-only)

## Presets

Presets appear in the curation modal so authors can reuse a size rather than re-entering it. Register them from a service provider:

```php
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Facades\Curation;

public function register(): void
{
    Curation::presets([
        CurationPreset::make('Thumbnail')
            ->width(200)
            ->height(200)
            ->format('webp')
            ->quality(80),
    ]);
}
```

The name you pass to `make()` is the label. Its **key** is that label slugged with underscores — `Thumbnail` becomes `thumbnail`, `Hero Banner` becomes `hero_banner` — and the key is what you reference when rendering.

> [!NOTE]
> Registering nothing does not mean no presets. Curator falls back to a single built-in `Thumbnail` preset, 200×200 webp at quality 60.

Choosing a preset in the modal locks the crop to the preset's shape, and the curation is saved at the preset's width and height. A custom curation, with a key of your own, is saved at the size of the crop in the original image.

### Size limits

Saving a curation needs the `update` ability on the media, the same as editing it. With no policy registered for the media model, anyone who can reach the edit page can save one.

The crop box is trimmed to the image, after any flip and rotation, so the part of a crop that hangs over the image's edge is dropped rather than padded. A crop that misses the image entirely is rejected. Because of that, a custom curation is never larger than its source image.

A custom curation is also capped by the `curation_max_dimension` config key, `8192` pixels by default. A crop whose longer side is bigger than that is scaled down to fit, keeping its shape. Set the key to `null` or `0` to turn the cap off. It doesn't apply to presets, which are always saved at the width and height they're registered with.

## Rendering a curation

```blade
<x-curator-curation :media="$media" curation="thumbnail" loading="lazy" />
```

`media` is required and takes a media id or a `Media` instance. `curation` is the preset key.

## Falling back to Glider

A curation only exists for images an author has actually curated, so check before rendering one and fall back to [the glider component](glider.md) otherwise. This keeps you from having to curate every image — only the ones whose crop matters.

```blade
@if ($media->hasCuration('thumbnail'))
    <x-curator-curation :media="$media" curation="thumbnail" />
@else
    <x-curator-glider
        class="object-cover w-auto"
        :media="$media"
        width="200"
        height="200"
    />
@endif
```

Keep the fallback's dimensions in step with the preset's. If you would rather not repeat them, read the registered presets back with `Curation::getPresets()`, which returns `CurationPreset` objects exposing `getKey()`, `getWidth()`, `getHeight()`, `getFormat()` and `getQuality()`.

Curations can be turned off entirely through the `features.curations` config key or `curations(false)` on the plugin — see [Configuration](../configuration.md).
