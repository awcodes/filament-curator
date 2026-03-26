<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    protected $guarded = [];

    public function gallery(): MorphMany
    {
        return $this->morphMany(Mediable::class, 'mediable');
    }
}
