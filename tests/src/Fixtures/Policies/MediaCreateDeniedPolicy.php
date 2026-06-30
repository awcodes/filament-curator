<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Policies;

use Awcodes\Curator\Tests\Fixtures\Models\User;

class MediaCreateDeniedPolicy
{
    public function create(User $user): bool
    {
        return false;
    }
}
