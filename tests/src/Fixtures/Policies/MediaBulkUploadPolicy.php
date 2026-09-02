<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Policies;

use Workbench\App\Models\User;

class MediaBulkUploadPolicy
{
    public function create(User $user): bool
    {
        // create is allowed, so a passing bulkUpload check must be what gates
        // the action — proving the dedicated ability takes precedence.
        return true;
    }

    public function bulkUpload(User $user): bool
    {
        return false;
    }
}
