---
title: Curations
description: Create custom crops and focal points per image, and render them with the curation component.
---

# Curations

A curation is a crop and focal point saved against one image under a named preset. Where Glide applies the same rule to every image, a curation lets an author decide how one particular image should be cropped.

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

## Rendering a curation

```blade
<x-curator-curation :media="$media" curation="thumbnail" loading="lazy" />
```

`media` is required and takes a media id or a `Media` instance. `curation` is the preset key.

## Falling back to Glider

A curation only exists for images an author has actually curated, so check before rendering one and fall back to [the glider component](glider.md) otherwise. This keeps you from having to curate every image — only the ones whose focal point matters.

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
