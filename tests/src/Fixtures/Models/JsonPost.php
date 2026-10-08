<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Models;

use Workbench\App\Models\Post;

/**
 * A post whose content column holds JSON, as a repeater, builder or group with a state path stores it.
 */
class JsonPost extends Post
{
    protected $table = 'posts';

    protected function casts(): array
    {
        return ['content' => 'array'];
    }
}
