---
title: Accepted file types
description: What Curator accepts by default, why certain types are excluded, and how to opt back in safely.
---

# Accepted file types

## The default list

When you do not set `acceptedFileTypes()` yourself, Curator falls back to `MimeType::defaults()` — the full `MimeType` list minus types that are effectively executable content:

`text/html`, `application/xhtml+xml`, `text/javascript`, `application/xml`, `application/vnd.mozilla.xul+xml`, `application/x-httpd-php`, `application/x-sh`, `application/x-csh`, `application/x-shockwave-flash` and `application/octet-stream`.

## Why those are excluded

Uploaded files are served from your application's own origin. An HTML or XML document containing a `<script>` tag executes with the session of whoever opens it, and with the default `public` disk the storage directory sits inside the document root, where a server may execute scripts directly.

`application/octet-stream` is excluded separately: it is what `finfo` reports for anything it cannot classify, so allowing it turns the allow list into a wildcard.

## Opting back in

You can allow these types, globally or per field, if your application genuinely needs to host them:

```php
use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;

// globally
Curator::acceptedFileTypes([...MimeType::defaults(), 'text/html']);
```

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Enums\MimeType;

// or per field
CuratorPicker::make('attachment')
    ->acceptedFileTypes([...MimeType::defaults(), 'text/html']);
```

> [!WARNING]
> Curator only sanitizes SVG uploads. Any other type you opt into is stored and served verbatim. Media served through Curator's route is sent with `X-Content-Type-Options: nosniff`, and restricted types are forced to `Content-Disposition: attachment`, but files on the `public` disk are also reachable directly through the `storage` symlink, where those headers do not apply. If you allow executable types, serve them from a private disk.
