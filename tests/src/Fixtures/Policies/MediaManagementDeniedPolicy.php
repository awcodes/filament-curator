<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Policies;

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Models\User;

class MediaManagementDeniedPolicy
{
    public function view(User $user, Media $media): bool
    {
        return false;
    }

    public function update(User $user, Media $media): bool
    {
        return false;
    }

    public function delete(User $user, Media $media): bool
    {
        return false;
    }
}
