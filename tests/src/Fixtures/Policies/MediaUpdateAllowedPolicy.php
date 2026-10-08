<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Policies;

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Models\User;

class MediaUpdateAllowedPolicy
{
    public function update(User $user, Media $media): bool
    {
        return true;
    }
}
