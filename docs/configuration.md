---
title: Configuration
description: Publish Curator's config file, register the plugin on a panel, swap in your own media model, and hook the delete actions.
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
| `default_directory` | Directory within the disk. Uploads land here, and the picker and rich editor panels open here unless given their own `directory()`. |
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

## Customising delete actions

Media can be deleted from three places: the resource table (row and bulk actions), the edit page, and the picker panel. `Curator::configureDeleteActionsUsing()` hooks all of them at once, so you can add a warning, a confirmation step, or a guard without touching Filament's global `DeleteAction::configureUsing()`. Call it from a service provider's `boot()` method:

```php
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Illuminate\Support\Collection;

Curator::configureDeleteActionsUsing(function (Action $action): void {
    if ($action instanceof BulkAction) {
        $action->modalDescription(fn (Collection $records): string => "Delete {$records->count()} files? Any content using them will lose its image.");

        return;
    }

    $action->modalDescription(fn (?Media $record): string => "Delete {$record?->name}? Any content using it will lose its image.");
});
```

The callback receives the action itself, so any Filament action method is available, including `before()` with `$action->halt()` to stop a deletion. Single-record actions receive the media as `$record` and the bulk action receives `$records`. Type `$record` as nullable: the picker panel only resolves it once the action is opened, and not at all if the user may not delete that item.

The hook doesn't change how deletes are authorized: the resource actions still check your media policy, and the picker panel still checks it again before deleting. Your callback can override that, though. Calling `authorize()` replaces the resource's policy check, and calling `action()` on the picker panel's action replaces its checked delete logic. Avoid both unless you intend to take over authorization.

If you have replaced the resource's page or table classes in the config, your versions need to pass their delete actions through `Curator::configureDeleteAction()` to pick up the hook.
