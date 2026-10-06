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

## Rendering saved content

Inserted media is stored as a plain `<img>` with the media's URL, alt text and title, so it renders through Filament's `RichContentRenderer` like any other image.

Content saved with earlier versions also stored the media's key in a `data-id` attribute. Filament treats `data-id` as a file attachment path, and its renderer replaces the `src` of any image that has one, so those images render without a `src`. Removing the attribute fixes them. For content stored as HTML, this removes it from images whose `data-id` is a media key (an integer, UUID or ULID), and leaves Filament's own file attachments, which store a path, untouched:

```php
$content = preg_replace(
    '/(<img\b[^>]*?)\s+data-id="(?:\d+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}|[0-9A-HJKMNP-TV-Z]{26})"/i',
    '$1',
    $content,
);
```

Run it once over each stored value, for example in a migration or a command. Content stored as JSON keeps the key in each image node's `attrs.id`. Clear that value on the same kinds of key.
