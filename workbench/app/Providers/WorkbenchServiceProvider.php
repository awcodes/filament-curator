<?php

declare(strict_types=1);

namespace Workbench\App\Providers;

use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Facades\Curation;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Focus serves the Workbench on a random port, so a root-relative URL keeps file URLs the same on every run.
        config(['filesystems.disks.public.url' => '/storage']);

        Curation::presets([
            CurationPreset::make('Post thumbnail')
                ->width(320)
                ->height(180)
                ->format('webp')
                ->quality(80),
        ]);
    }
}
