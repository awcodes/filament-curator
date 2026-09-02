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
        Curation::presets([
            CurationPreset::make('Post thumbnail')
                ->width(320)
                ->height(180)
                ->format('webp')
                ->quality(80),
        ]);
    }
}
