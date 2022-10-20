<?php

namespace FilamentCurator\Config\PathGenerator;

use Carbon\Carbon;

class WordpressPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir): string
    {
        $now = Carbon::now();

        return ($baseDir ? $baseDir . '/' : '') . sprintf(
            '%s/%s/%s/',
            $now->format('Y'),
            $now->format('m'),
            $now->format('d')
        );
    }
}
