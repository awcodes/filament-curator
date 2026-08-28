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
