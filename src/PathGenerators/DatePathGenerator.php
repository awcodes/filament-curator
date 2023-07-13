<?php

namespace Awcodes\Curator\PathGenerators;

use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Carbon\Carbon;

class DatePathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        $now = Carbon::now();

        return ($baseDir ? $baseDir . '/' : '') . sprintf(
            '%s/%s/%s',
            $now->format('Y'),
            $now->format('m'),
            $now->format('d')
        );
    }
}
