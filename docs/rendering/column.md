---
title: Table column
description: Render media in a Filament table with CuratorColumn, and avoid N+1 queries.
---

# Table column

`CuratorColumn` renders media in a table. It extends Filament's `ImageColumn`, so everything that works there works here.

```php
use Awcodes\Curator\Components\Tables\CuratorColumn;

CuratorColumn::make('featured_image')
    ->size(40);
```

## Multiple images

For a relationship holding several images, control how many are shown and how they stack:

```php
use Awcodes\Curator\Components\Tables\CuratorColumn;

CuratorColumn::make('product_pictures')
    ->ring(2)
    ->overlap(4)
    ->limit(3);
```

`ring()` accepts `0`, `1`, `2` or `4`, and `overlap()` accepts `0`, `2`, `3` or `4`.

## Avoiding N+1 queries

A column backed by a relationship will query once per row unless you eager load it. In a panel resource's list page:

```php
protected function getTableQuery(): Builder
{
    return parent::getTableQuery()->with(['featured_image', 'product_pictures']);
}
```

Or from a table class:

```php
public static function configure(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->with('media', 'gallery'));
}
```
