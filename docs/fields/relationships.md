---
title: Relationships
description: Bind the Curator picker to a single media relationship or to many.
---

# Relationships

## A single image

Point the picker at the foreign key column and name the relationship:

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;

CuratorPicker::make('featured_image_id')
    ->relationship('featured_image', 'id');
```

Then define it on the model:

```php
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

public function featuredImage(): BelongsTo
{
    return $this->belongsTo(Media::class, 'featured_image_id', 'id');
}
```

## Many images

Add `multiple()`. Without it the picker will not let the author select more than one item, whatever the relationship allows.

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;

CuratorPicker::make('product_picture_ids')
    ->multiple()
    ->relationship('product_pictures', 'id')
    ->orderColumn('order');
```

`orderColumn()` is only needed if your pivot's order column is named something other than `order`.

The relationship carries the order on its pivot:

```php
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

public function productPictures(): BelongsToMany
{
    return $this
        ->belongsToMany(Media::class, 'media_post', 'post_id', 'media_id')
        ->withPivot('order')
        ->orderBy('order');
}
```

> [!TIP]
> Rendering these in a table will cause N+1 queries unless you eager load them — see [Table column](../rendering/column.md).
