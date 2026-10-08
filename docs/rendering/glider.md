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
| `srcset` | An array of widths (`640w`) or pixel densities (`2x`). Requires `sizes` to be set as well. |
| `sizes` | The `sizes` attribute paired with `srcset`. |
| `fallback` | Name of a registered fallback — see below. |

Any other attribute, such as `class` or `loading="lazy"`, is passed through to the `<img>`.

Glide URLs are relative to your site's root, like `/curator/photo.jpg?w=1024&s=…`. That works on your own pages, but HTML read somewhere else, such as an email, a feed or an API response, needs absolute URLs. Build those with Laravel's `url()` helper, for example `url($media->getGlideUrl(['w' => 1024]))`.

For media that isn't public, the component builds [temporary URLs](../storage/glide.md#private-media) that expire after a few minutes. Pass private media as a `Media` instance or an id: a path given on its own builds a permanent URL, which the route only serves for public media.

The image's `width` and `height` attributes describe what Glide will serve. Give only one of them and the other is worked out from the media's aspect ratio, so the box reserves the right space and the layout doesn't shift when the image loads.

If the media item can't be found (a null value, or an id whose record was deleted) and no fallback is given, the component renders nothing. An id that no longer resolves is also logged as a warning.

Glide's own parameters are passed as attributes: `background`, `blur`, `border`, `brightness`, `contrast`, `crop`, `device-pixel-ratio`, `filter`, `fit`, `flip`, `format`, `gamma`, `height`, `quality`, `orientation`, `pixelate`, `sharpen`, `width`, and the `watermark-*` family. See [Glide's quick reference](https://glide.thephpleague.com/2.0/api/quick-reference/) for what each accepts.

Responsive images need both `srcset` and `sizes`:

```blade
<x-curator-glider
    :media="1"
    :srcset="['1024w', '640w']"
    sizes="(max-width: 1200px) 100vw, 1024px"
/>
```

A pixel density keeps the requested size and asks Glide for that device pixel ratio, which suits images shown at a fixed size:

```blade
<x-curator-glider
    :media="1"
    width="400"
    :srcset="['1x', '2x']"
    sizes="400px"
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

A source that is a path in your `public` directory, like the one above, is linked as a plain asset, without Glide's transformations. A source that is the path of a stored media item goes through Glide like any other media. A full URL is used as-is.

> [!WARNING]
> A missing fallback is treated as a configuration mistake, not missing media, so the component throws. That covers a name that was never registered and a fallback that ends up without a source.

Reference it by name:

```blade
<x-curator-glider :media="1" fallback="thumbnail" />
```
