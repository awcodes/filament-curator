<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Post extends Model
{
    protected $guarded = [];

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function gallery(): MorphMany
    {
        return $this->morphMany(Mediable::class, 'mediable');
    }

    public function galleryMedia(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable', 'mediables', relatedPivotKey: 'media_id')
            ->orderByPivot('order');
    }
}
