<?php

declare(strict_types=1);

namespace Awcodes\Curator\PathGenerators;

use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Carbon\Carbon;

class DatePathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        $now = Carbon::now();

        return ($baseDir !== null && $baseDir !== '' && $baseDir !== '0' ? $baseDir.'/' : '').sprintf(
            '%s/%s/%s',
            $now->format('Y'),
            $now->format('m'),
            $now->format('d')
        );
    }
}
