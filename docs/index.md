---
title: Curator
description: A media picker and manager for Filament, with Glide-backed image transformations and per-image curations.
---

# Curator

Curator is a media picker and manager for Filament. It stores uploaded files in a `curator` table alongside their metadata, gives you a modal for picking or uploading them from any form, and serves them through [Glide](https://glide.thephpleague.com) so you can transform images at render time.

> [!WARNING]
> Curator does not work with Spatie Media Library. The two manage media in incompatible ways.

## What it gives you

- **A picker field** — `CuratorPicker` opens a modal to select existing media or upload new files, with single and multiple relationship support.
- **A media manager** — a Filament resource for browsing, editing and organising everything that has been uploaded.
- **Image transformation** — the `<x-curator-glider>` Blade component builds signed Glide URLs, so sizing, cropping and format conversion happen on request.
- **Curations** — per-image crops and focal points saved against a named preset, for when a single automatic crop is not good enough.

## Compatibility

| Package version | Filament version |
|-----------------|------------------|
| 1.x             | 2.x              |
| 2.x             | 2.x              |
| 3.x             | 3.x              |
| 4.x             | 4.x              |
| 5.x             | 4.x & 5.x        |

Curator requires PHP 8.2 or later and `filament/filament`. It pulls in `league/glide` for image transformation and `enshrined/svg-sanitize` for cleaning SVG uploads.

## Where to go next

- [Installation](installation.md) — install the package and run the installer.
- [Configuration](configuration.md) — the config file and registering the plugin on a panel.
- [Accepted file types](file-types.md) — what Curator allows by default, and why.
- [Picker field](fields/picker.md) — putting the picker in a form.
- [Glider component](rendering/glider.md) — rendering media on the front end.
- [Glide token](storage/token.md) — the one thing you must set in every environment.
