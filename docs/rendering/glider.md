---
title: Glider component
description: Render media through Glide with the x-curator-glider Blade component.
---

# Glider component

`<x-curator-glider>` renders a media item through Glide, building a signed URL with whatever transformations you ask for.

```blade
<div class="aspect-video w-64">
    <x-curator-glider
        class="object-cover w-auto"
        :media="$media"
        width="1024"
        format="webp"
    />
</div>
```

## Component attributes

| Attribute | Purpose |
|---|---|
| `media` | **Required.** A media id or a `Media` instance. |
| `loading` | Defaults to `lazy`. |
| `glide` | A raw Glide query string, if you would rather not use individual attributes. |
| `srcset` | An array of widths. Requires `sizes` to be set as well. |
| `sizes` | The `sizes` attribute paired with `srcset`. |
| `fallback` | Name of a registered fallback — see below. |
| `force` | Forces a signed URL. Useful for cloud disks, with possible performance cost. |

Glide's own parameters are passed as attributes: `background`, `blur`, `border`, `brightness`, `contrast`, `crop`, `device-pixel-ratio`, `filter`, `fit`, `flip`, `format`, `gamma`, `height`, `quality`, `orientation`, `pixelate`, `sharpen`, `width`, and the `watermark-*` family. See [Glide's quick reference](https://glide.thephpleague.com/2.0/api/quick-reference/) for what each accepts.

Responsive images need both `srcset` and `sizes`:

```blade
<x-curator-glider
    :media="1"
    :srcset="['1024w', '640w']"
    sizes="(max-width: 1200px) 100vw, 1024px"
/>
```

## Fallbacks

Register named fallbacks to use when a media item does not exist:

```php
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GliderFallback;

public function register(): void
{
    Glide::registerGliderFallbacks([
        GliderFallback::make('thumbnail')
            ->alt('Placeholder')
            ->source('/images/placeholder.jpg')
            ->width(200)
            ->height(200),
    ]);
}
```

Everything but the name is optional and may be null, so a conditional value is fine.

> [!WARNING]
> A fallback that ends up without a source cannot be rendered, and referencing it from the component will throw.

Reference it by name:

```blade
<x-curator-glider :media="1" fallback="thumbnail" />
```
