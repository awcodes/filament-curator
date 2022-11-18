<?php

namespace FilamentCurator;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use FilamentCurator\Commands\InstallCommand;
use FilamentCurator\Commands\RegenerateThumbnails;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;

class FilamentCuratorServiceProvider extends PluginServiceProvider
{
    protected array $beforeCoreScripts = [
        'https://unpkg.com/@alpinejs/intersect@3.x.x/dist/cdn.min.js',
    ];

    protected function getStyles(): array
    {
        $styles = config('filament-curator.load_styles') ? [
            'filament-curator-styles' => __DIR__ . '/../resources/dist/filament-curator.css',
        ] : [];

        return $styles;
    }

    protected function getResources(): array
    {
        return [
            config('filament-curator.media_resource'),
        ];
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-curator')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasTranslations()
            ->hasCommands([
                InstallCommand::class,
                RegenerateThumbnails::class,
            ])
            ->hasMigrations(['create_media_table']);
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton('curator-thumbnails', function (): CuratorThumbnails {
            return new CuratorThumbnails();
        });
    }

    public function boot(): void
    {
        parent::boot();

        Livewire::component('curator', Forms\Components\Curator::class);
    }
}
