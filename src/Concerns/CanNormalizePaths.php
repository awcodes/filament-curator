<?php

namespace Awcodes\Curator\Concerns;

use Illuminate\Support\Str;

trait CanNormalizePaths
{
    public function normalizePath(string $path): string
    {
        // normalization /path//to/dir/ --> path/to/dir
        $path = preg_replace('#/+#', '/', $path);
        if (Str::startsWith($path, '/')) {
            $path = substr($path, 1);
        }
        if (Str::endsWith($path, '/')) {
            $path = substr($path, 0, strlen($path) - 1);
        }

        return $path;
    }
}
