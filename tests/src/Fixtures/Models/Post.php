<?php

namespace Awcodes\Curator\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    protected $casts = [
        'gallery' => 'array',
        'content' => 'array',
        'meta' => 'array',
        'settings' => 'array',
    ];
}
