<?php

namespace RocketFirm\Curator\Generators;

use Carbon\Carbon;

class DatePathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        $now = Carbon::now();

        return ($baseDir ? $baseDir.'/' : '').sprintf(
            '%s/%s/%s',
            $now->format('Y'),
            $now->format('m'),
            $now->format('d')
        );
    }
}
