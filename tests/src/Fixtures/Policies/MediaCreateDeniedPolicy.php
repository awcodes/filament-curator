<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Policies;

use Workbench\App\Models\User;

class MediaCreateDeniedPolicy
{
    public function create(User $user): bool
    {
        return false;
    }
}
