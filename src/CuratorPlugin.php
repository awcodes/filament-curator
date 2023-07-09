<?php

namespace Awcodes\Curator;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Session;

class CuratorPlugin implements Plugin
{
    public function getId(): string
    {
        return 'awcodes/curator';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                config('curator.media_resource'),
            ]);
    }

    public function boot(Panel $panel): void
    {
        if (!Session::has('tableLayout')) {
            Session::put('tableLayout', config('curator.table_has_grid_layout'));
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}