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

## Supplying your own server

Pass a Glide server configuration to the facade to take over how media is served:

```php
use Awcodes\Curator\Facades\Glide;
use Illuminate\Support\Facades\Storage;

public function register(): void
{
    Glide::serverConfig([
        'driver' => 'imagick',
        'response' => new LaravelResponseFactory(app('request')),
        'source' => Storage::disk('public')->getDriver(),
        'cache' => storage_path('app'),
        'cache_path_prefix' => '.cache',
        'max_image_size' => 2000 * 2000,
    ]);
}
```

## Cloud disks

Out of the box, Glide reads each media item from the disk it was stored on, through that disk's Flysystem driver, so S3, MinIO and Laravel's `local` disk work without extra configuration. Transformed images are cached locally in `storage/app/.cache`.

A config passed to `Glide::serverConfig()` replaces that default completely, and its `source` is used for every disk. If you supply one:

```php
use Awcodes\Curator\Facades\Glide;
use Illuminate\Support\Facades\Storage;

Glide::serverConfig([
    'response' => new LaravelResponseFactory(app('request')),
    'source' => Storage::disk('s3')->getDriver(),
    'cache' => Storage::disk('local')->getDriver(),
    'cache_path_prefix' => '.cache',
    'max_image_size' => 2000 * 2000,
]);
```

- **Point `source` at the disk your media lives on, with no `source_path_prefix`.** Curator stores each file's `path` relative to its disk. Media on any other disk will fail to render.
- **Keep `cache` on a fast local disk.** Transformed images are cached there, so only the first request per variant reads from the cloud.

Rendering from a cloud disk may also need `force` on the glider component to get a signed URL — see [Glider component](../rendering/glider.md).
