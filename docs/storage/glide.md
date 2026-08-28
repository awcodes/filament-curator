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

public function register(): void
{
    Glide::serverConfig([
        'driver' => 'imagick',
        'response' => new LaravelResponseFactory(app('request')),
        'source' => storage_path('app'),
        'source_path_prefix' => 'public',
        'cache' => storage_path('app'),
        'cache_path_prefix' => '.cache',
        'max_image_size' => 2000 * 2000,
    ]);
}
```

## Cloud disks

> [!IMPORTANT]
> The default configuration points Glide's `source` at the local filesystem. If your media lives on S3, MinIO or similar you **must** point `source` at that disk's Flysystem driver, or Glide cannot find the source images and they will not render.

```php
use Awcodes\Curator\Facades\Glide;
use Illuminate\Support\Facades\Storage;

Glide::serverConfig([
    'response' => new LaravelResponseFactory(app('request')),
    'source' => Storage::disk('s3')->getDriver(),
    'source_path_prefix' => '',
    'cache' => Storage::disk('local')->getDriver(),
    'cache_path_prefix' => '.cache',
    'max_image_size' => 2000 * 2000,
]);
```

Three things to watch:

- **`source_path_prefix` must match where your objects actually live.** A cloud disk's Flysystem is already rooted at the bucket, and Curator stores each file's `path` relative to it, so this is usually an empty string. The `'public'` prefix in the local default exists only because that source is rooted at `storage_path('app')` while files sit under `storage/app/public/`. A mismatched prefix is the most common cause of images not rendering on cloud disks.
- **Keep `cache` on a fast local disk.** Transformed images are cached there, so only the first request per variant reads from the cloud. A cold cache against a remote source is slow; a warm local one is not.
- **Watch for stray media on another disk.** Records still pointing at `public` while your source is S3 will fail lookups and slow things down. Make sure existing records' `disk` matches your Glide source.

Rendering from a cloud disk may also need `force` on the glider component to get a signed URL — see [Glider component](../rendering/glider.md).
