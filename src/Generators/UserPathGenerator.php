<?php

namespace Awcodes\Curator\Generators;

use Illuminate\Support\Facades\Auth;

class UserPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        $user = Auth::user();

        return ($baseDir ? $baseDir.'/' : '').$user->getAuthIdentifier();
    }
}
