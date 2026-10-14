---
title: Rich editor
description: Attach Curator media from inside Filament's RichEditor field.
---

# Rich editor

Curator ships a plugin for Filament's `RichEditor` that adds an "attach media" tool, opening the same picker modal used elsewhere.

```php
use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Filament\Forms\Components\RichEditor;

RichEditor::make('content')
    ->tools([
        'attachCuratorMedia',
    ])
    ->plugins([
        AttachCuratorMediaPlugin::make(),
    ]);
```

Both halves are required: `plugins()` registers the tool, and `tools()` places it in the toolbar. Registering the plugin without naming the tool leaves it unreachable.

The modal lists and inserts media the way a picker does — see [What a picker lists](picker.md#what-a-picker-lists). It lists media on Curator's disk (`Curator::disk()`, or the `default_disk` config value), unless you set a file attachment disk on the editor with `fileAttachmentsDisk()` or on the model's rich content attribute, and uploads from the modal go to the disk it lists. Visibility works the same way. It opens in `fileAttachmentsDirectory()` and accepts `fileAttachmentsAcceptedFileTypes()`, using the `Curator` facade's directory and accepted types when those aren't set. Tenancy follows the facade's setting. It isn't limited to its directory.

## Rendering saved content

By default, inserted media is stored as a plain `<img>` with the media's URL, alt text and title, so it renders through Filament's `RichContentRenderer` like any other image. The URL is fixed when the media is inserted: replacing the file or moving the disk leaves the content pointing at the old one.

## Storing media ids

To have images follow their media, store each one's key in the image's `data-id`, the attribute Filament uses for its own file attachments:

```php
AttachCuratorMediaPlugin::make()->storeMediaIds()
```

Filament reads `data-id` through the editor's file attachment provider, so the plugin then supplies `CuratorFileAttachmentProvider`. Each time the content is edited or rendered, the provider looks the key up in the library and gives the image the media's current URL. A key naming media that has been deleted, or that isn't public, gets no URL. Images uploaded, dropped or pasted into the editor are added to the library through the same checks as the picker's uploads (detected file type, SVG sanitizing, size), and stored publicly.

Filament takes the provider from the plugin wherever it reads the plugins on their own: a model's rich content attribute, and `RichContentRenderer`. Render with the same plugin so the URLs are filled in:

```php
use Filament\Forms\Components\RichEditor\RichContentRenderer;

RichContentRenderer::make($content)
    ->plugins([AttachCuratorMediaPlugin::make()->storeMediaIds()])
    ->toHtml();
```

A field that isn't bound to a rich content attribute, such as one inside a nested form, doesn't consult its plugins for a provider. Give the field the provider's lookup and upload yourself:

```php
use Awcodes\Curator\Components\Forms\RichEditor\CuratorFileAttachmentProvider;

$provider = CuratorFileAttachmentProvider::make();

RichEditor::make('content')
    ->plugins([AttachCuratorMediaPlugin::make()->storeMediaIds(provider: $provider)])
    ->getFileAttachmentUrlUsing(fn (mixed $file): ?string => $provider->getFileAttachmentUrl($file))
    ->saveUploadedFileAttachmentUsing(fn ($file): mixed => $provider->saveUploadedFileAttachment($file));
```

The provider uploads to Curator's disk, directory and accepted types, and caps uploads at Curator's maximum size. Set them on the provider with `disk()`, `directory()`, `acceptedFileTypes()` and `maxSize()` (in kilobytes), and pass it to `storeMediaIds(provider: ...)`. A provider of your own can be passed the same way, as long as it reads media keys.

Filament deletes attachments a saved document no longer uses by calling the provider's `cleanUpFileAttachments()`. Curator's does nothing there: the library is shared, so media stays until it is deleted from the library.

If the editor uses `preventFileAttachmentPathTampering()`, allow the ids the provider resolves, since media inserted from the library was never uploaded to the record:

```php
->preventFileAttachmentPathTampering(allowFilePathUsing: fn (string $file): bool => $provider->findMedia($file) !== null)
```

## Content saved before 5.5

Content saved with versions before 5.5 stored the media's key in `data-id` whether or not a provider could read it. Filament treats `data-id` as a file attachment path, and without a provider its renderer replaces the `src` of any image that has one, so those images render without a `src`.

Turning on `storeMediaIds()` fixes them, since the provider resolves the keys. Otherwise remove the attribute. For content stored as HTML, this removes it from images whose `data-id` is a media key (an integer, UUID or ULID), and leaves Filament's own file attachments, which store a path, untouched:

```php
$content = preg_replace(
    '/(<img\b[^>]*?)\s+data-id="(?:\d+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}|[0-9A-HJKMNP-TV-Z]{26})"/i',
    '$1',
    $content,
);
```

Run it once over each stored value, for example in a migration or a command. Content stored as JSON keeps the key in each image node's `attrs.id`. Clear that value on the same kinds of key.
