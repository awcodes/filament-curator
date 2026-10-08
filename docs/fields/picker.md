---
title: Picker field
description: Add the Curator picker to a form to select existing media or upload new files.
---

# Picker field

`CuratorPicker` opens Curator's modal so an author can pick existing media or upload something new. Many of Filament's `FileUpload` methods work on it too, for per-field sizing and validation.

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Support\Enums\Size;

CuratorPicker::make('featured_image_id')
    ->label('Featured image')
    ->buttonLabel('Choose an image')
    ->size(Size::Medium)
    ->constrained();
```

![A Curator picker field labelled Featured image, showing the selected Mountain dusk image with its file size and an actions menu](../assets/picker-light.png#gh-light-mode-only)
![A Curator picker field labelled Featured image, showing the selected Mountain dusk image with its file size and an actions menu](../assets/picker-dark.png#gh-dark-mode-only)

The trigger opens Curator's library in a full-screen modal, where an author picks existing media or uploads new files:

![The Curator picker modal: a grid of eight image thumbnails with the first one selected, Insert and Deselect All buttons, and a sidebar listing the selected file above an upload area](../assets/picker-modal-light.png#gh-light-mode-only)
![The Curator picker modal: a grid of eight image thumbnails with the first one selected, Insert and Deselect All buttons, and a sidebar listing the selected file above an upload area](../assets/picker-modal-dark.png#gh-dark-mode-only)

## Appearance

| Method | Effect |
|---|---|
| `buttonLabel()` | Text on the trigger button. |
| `color()` | Trigger colour. Defaults to gray. |
| `outlined()` | Outlined trigger. Defaults to `true`. |
| `size()` | Trigger size, taking a `Size` enum case. |
| `constrained()` | Fits the image inside the preview area. Defaults to `false`. |
| `listDisplay()` | Shows selections as a list instead of a grid. Off unless you call it. |
| `lazyLoad()` | Lazy-loads previews. Off unless you call it. |
| `defaultPanelSort()` | Sort direction for the picker panel. Defaults to `desc`. |

## Selection and storage

| Method | Effect |
|---|---|
| `multiple()` | Allow more than one item. Required when using a relationship with multiple media. |
| `maxItems()` | Cap how many items can be selected. |
| `relationship()` | Bind to a relationship — see [Relationships](relationships.md). |
| `orderColumn()` | Rename the order column used by multiple relationships. Defaults to `order`. |
| `pathGenerator()` | Where uploads are written — see [Path generation](../storage/paths.md). |
| `limitToDirectory()` | Restrict the picker to its `directory()` and the folders below it — see [What a picker lists](#what-a-picker-lists). Without it, the `features.directory_restriction` config value decides, which is off by default. |
| `tenantAware()` | Scope to the current tenant. Without it, the `features.tenancy.enabled` config value decides, which is off by default. |

Uploads also accept the familiar Filament methods — `preserveFilenames()`, `minSize()`, `maxSize()`, `rules()`, `acceptedFileTypes()`, `disk()`, `visibility()`, `directory()`, `imageCropAspectRatio()`, `imageResizeMode()`, `imageResizeTargetWidth()` and `imageResizeTargetHeight()`. See Filament's [file upload documentation](https://filamentphp.com/docs/5.x/forms/file-upload) for what each does.

`rules()` validates the picker's selection, and its rules that describe a single file, such as `dimensions:min_width=1200` or `mimes:jpg,png`, also apply to each upload. Rules about the selection itself, such as `required`, `min` and `max` (which count selected items here, not kilobytes), don't apply to uploads. Use `minSize()` and `maxSize()` for file sizes.

To limit image dimensions, reject uploads with a `dimensions` rule, or have the browser shrink them before upload instead:

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;

// Reject images wider than 2000px.
CuratorPicker::make('featured_image_id')
    ->rules(['dimensions:max_width=2000']);

// Or scale them down to 2000px wide before they are uploaded.
CuratorPicker::make('featured_image_id')
    ->imageResizeMode('contain')
    ->imageResizeTargetWidth('2000');
```

`maxWidth()` isn't an upload limit. It's Filament's layout method, which sets how wide the field itself is.

A picker that holds one item accepts one upload at a time, and uploading replaces the current selection.

The media panel takes these settings from the picker when it opens, and they can't be changed from the browser while it's open. Uploads go to the picker's `directory()`, or to an existing folder the user has browsed into. If you render the `curator-panel` Livewire component yourself, pass its configuration through the `settings` array when you mount it; setting its properties from the browser afterwards is rejected. When media is inserted, the panel sends the picker the stored records for the selected ids.

> [!NOTE]
> `acceptedFileTypes()` defaults to Curator's own safe list rather than allowing everything — see [Accepted file types](../file-types.md).

## What a picker lists

A picker only works with the media its own settings allow:

- **Its disk.** Media stored on another disk isn't listed, even when it has the same directory.
- **The types it accepts.** `acceptedFileTypes()` applies to the library as well as to uploads, and in the same way: `image/*` matches any image, SVG included, but a wildcard never matches a type that can run script, such as `text/html` under `text/*`. List a type exactly to include it.
- **The current tenant**, when tenancy applies. With `tenantAware()` (or the `features.tenancy.enabled` config value), the picker filters on the tenant itself. When Curator's media resource is registered on the tenant panel, Filament's own tenant scope applies as well.
- **Its directory**, with `limitToDirectory()`. The panel opens in the picker's `directory()` and can't leave it: the disk and the folders above aren't offered, and search, the folder list and uploads stay within it and the folders below it. Without `limitToDirectory()`, `directory()` is only where the panel opens and where uploads go.

The library listing, search, folder list, insert and download in the panel all use these settings, and so does the picker itself:

- A saved value is loaded again within them, so an id the picker wouldn't list, such as another tenant's media or one on another disk, doesn't load. It's left out of the field, and saving the form removes it.
- Media the panel sends back to the picker is looked up again, so the picker holds the stored records.
- Saving a selection that holds media outside these settings, or media that no longer exists, fails validation on the field.

If a picker needs existing media that its settings now leave out, for example after changing its `disk()`, widen the settings or move the media; it isn't shown otherwise.
