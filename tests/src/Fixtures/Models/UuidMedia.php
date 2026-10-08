<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Media with a UUID key, as the installer's UUID option creates it. Its table is created by the tests that use it.
 */
class UuidMedia extends Media
{
    use HasUuids;

    protected $table = 'uuid_curator';
}
