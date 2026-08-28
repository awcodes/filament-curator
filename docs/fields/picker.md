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

## Appearance

| Method | Effect |
|---|---|
| `buttonLabel()` | Text on the trigger button. |
| `color()` | Trigger colour. Defaults to gray. |
| `outlined()` | Outlined trigger. Defaults to `true`. |
| `size()` | Trigger size, taking a `Size` enum case. |
| `constrained()` | Fits the image inside the preview area. Defaults to `false`. |
| `listDisplay()` | Shows selections as a list. Defaults to `true`. |
| `lazyLoad()` | Lazy-loads previews. Defaults to `true`. |
| `defaultPanelSort()` | Sort direction for the picker panel. Defaults to `desc`. |

## Selection and storage

| Method | Effect |
|---|---|
| `multiple()` | Allow more than one item. Required when using a relationship with multiple media. |
| `maxItems()` | Cap how many items can be selected. |
| `relationship()` | Bind to a relationship — see [Relationships](relationships.md). |
| `orderColumn()` | Rename the order column used by multiple relationships. Defaults to `order`. |
| `pathGenerator()` | Where uploads are written — see [Path generation](../storage/paths.md). |
| `limitToDirectory()` | Restrict the picker to its own directory. Requires the `directory_restriction` feature. |
| `tenantAware()` | Scope to the current tenant. Defaults to `true`. |

Uploads also accept the familiar Filament methods — `preserveFilenames()`, `maxWidth()`, `minSize()`, `maxSize()`, `rules()`, `acceptedFileTypes()`, `disk()`, `visibility()`, `directory()`, `imageCropAspectRatio()`, `imageResizeMode()`, `imageResizeTargetWidth()` and `imageResizeTargetHeight()`. See Filament's [file upload documentation](https://filamentphp.com/docs/5.x/forms/file-upload) for what each does.

> [!NOTE]
> `acceptedFileTypes()` defaults to Curator's own safe list rather than allowing everything — see [Accepted file types](../file-types.md).
