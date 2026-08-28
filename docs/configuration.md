---
title: Configuration
description: Publish Curator's config file, register the plugin on a panel, and swap in your own media model.
---

# Configuration

## The config file

Global defaults live in the config file. The installer publishes it only if you chose UUID or tenancy support; otherwise publish it yourself when you need it:

```bash
php artisan vendor:publish --tag="curator-config"
```

| Key | Purpose |
|---|---|
| `default_disk` | Disk uploads are written to. Falls back to `CURATOR_DEFAULT_DISK`, then `FILESYSTEM_DISK`, then `public`. |
| `default_directory` | Directory within the disk. |
| `default_visibility` | Visibility applied to uploads, `public` by default. |
| `curation_formats` | Formats offered when creating a curation. |
| `features.curations` | Whether curations are available. |
| `features.file_swap` | Whether an existing file can be swapped for a new one. |
| `features.directory_restriction` | Whether pickers can be limited to their own directory. |
| `features.preserve_file_names` | Whether original filenames are kept. |
| `features.tenancy` | `enabled` plus the `relationship_name` to scope by. |
| `glide_token` | Reads `CURATOR_GLIDE_TOKEN`. See [Glide token](storage/token.md). |
| `model` | The media model. See below. |
| `path_generator` | Default path generator. See [Path generation](storage/paths.md). |
| `url_provider` | Class that builds media URLs, `GlideUrlProvider` by default. |
| `resource` | Labels, navigation, and the resource, page, schema and table classes Curator registers. |

## Registering the plugin on a panel

With Filament Panels, add the plugin to register Curator's resources. Every method is optional — anything you leave out is read from the config file.

```php
use Awcodes\Curator\CuratorPlugin;
use Filament\Support\Icons\Heroicon;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            CuratorPlugin::make()
                ->label('Media')
                ->pluralLabel('Media')
                ->navigationIcon(Heroicon::OutlinedPhoto)
                ->navigationGroup('Content')
                ->navigationSort(3)
                ->showBadge(true)
                ->registerNavigation(true)
                ->curations(true)
                ->fileSwap(true),
        ]);
}
```

Each of these accepts a closure as well as a plain value.

## Using your own model

To add behaviour to media records, extend Curator's model and point the config at yours:

```php
namespace App\Models;

use Awcodes\Curator\Models\Media as CuratorMedia;

class Media extends CuratorMedia
{
    //
}
```

```php
'model' => \App\Models\Media::class,
```

There is no need to set `$table` — the parent already points at `curator`. Only declare it if you have actually renamed the table.

The installer writes this model for you if you chose UUID or tenancy support, adding the `HasUuids` trait and the tenant relationship as needed.
