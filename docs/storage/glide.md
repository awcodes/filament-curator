---
title: Glide server
description: Change Curator's media route or supply your own Glide server, including on cloud disks.
---

# Glide server

## Changing the route

Curator serves images through the `curator` route by default. Change it from a service provider:

```php
use Awcodes\Curator\Facades\Glide;

public function register(): void
{
    Glide::basePath('media');
}
```

## Private media

Glide URLs for public media are permanent. They never change for a given file and are served with a year-long public `Cache-Control`, so front-end pages and CDNs can cache them.

Media whose visibility isn't `public` gets temporary URLs instead, wherever Curator builds one: the model's `thumbnail_url`, `medium_url` and `large_url`, the [Glider component](../rendering/glider.md) and the [table column](../rendering/column.md). A temporary URL carries a signed expiry and the media's disk. Once it expires, or if either value is changed, the route refuses it with a 403. While it is valid, the response is sent with `Cache-Control: private` and a `max-age` that runs out with the URL, so shared caches don't keep it. A URL without an expiry only ever serves public media.

This also covers files Glide can't transform, such as PDFs, video and SVGs. The size URLs for those stream the original file, so for private media they expire in the same way.

The lifetime is set in minutes by `temporary_url_expiration` in the config file, 5 by default. It also applies to the temporary disk URL the model's `url` returns for private media. Expiry times are rounded up to the minute, so pages that re-render keep the same URLs for a while.

Temporary URLs come out of a request for a page, so they don't belong anywhere they will be kept, such as cached HTML, a rich editor's content or an email. Build one in your own code with the model or the facade:

```php
use Awcodes\Curator\Facades\Glide;

$media->getGlideUrl(['w' => 800]); // temporary when the media isn't public

Glide::getTemporaryUrl($media->path, ['w' => 800], now()->addHour(), $media->disk);
```

`glide()->getUrl()` and `GlideBuilder::toUrl()` still build permanent URLs, so they only work for public media. A custom URL provider that builds its URLs with either of them gets temporary URLs for private media automatically.

An app that supplies its own server with `Glide::serverConfig()` reads every disk through that config's single `source` and `cache`, so it doesn't get the per-disk source or cache folders described under [Cloud disks](#cloud-disks). Temporary URLs and their expiry still apply.

### After upgrading

- **Purge Glide's cache** (`storage/app/.cache` by default) and any CDN in front of the media route. Images transformed before the upgrade for disks other than the default sit in the shared cache folder, and copies of earlier permanent URLs for private media can stay in CDN and browser caches for up to a year.
- **Rotate `CURATOR_GLIDE_TOKEN`** (`php artisan curator:token`) if earlier URLs for private media may have been shared. This invalidates every URL issued before, public ones included, so pages and content that stored Glide URLs need them rebuilt.

## Supplying your own server

Pass a Glide server configuration to the facade to take over how media is served:

```php
use Awcodes\Curator\Facades\Glide;
use Illuminate\Support\Facades\Storage;

public function register(): void
{
    Glide::serverConfig([
        'driver' => 'imagick',
        'source' => Storage::disk('public')->getDriver(),
        'cache' => storage_path('app'),
        'cache_path_prefix' => '.cache',
        'max_image_size' => 2000 * 2000,
    ]);
}
```

## Cloud disks

Out of the box, Glide reads each media item from the disk it was stored on, through that disk's Flysystem driver, so S3, MinIO and Laravel's `local` disk work without extra configuration. Transformed images are cached locally in `storage/app/.cache`, with media from disks other than the default kept in a folder of their own beneath it.

A config passed to `Glide::serverConfig()` replaces that default completely, and its `source` is used for every disk. If you supply one:

```php
use Awcodes\Curator\Facades\Glide;
use Illuminate\Support\Facades\Storage;

Glide::serverConfig([
    'source' => Storage::disk('s3')->getDriver(),
    'cache' => Storage::disk('local')->getDriver(),
    'cache_path_prefix' => '.cache',
    'max_image_size' => 2000 * 2000,
]);
```

- **Point `source` at the disk your media lives on, with no `source_path_prefix`.** Curator stores each file's `path` relative to its disk. Media on any other disk will fail to render.
- **Keep `cache` on a fast local disk.** Transformed images are cached there, so only the first request per variant reads from the cloud.
- **Don't rely on `base_url`.** Curator always passes Glide the media's own path, so a `base_url` in your config is ignored.

Curator adds the response factory itself, for the current request, so the config doesn't need a `response` entry.
