<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Observers\MediaObserver;
use Awcodes\Curator\Resources\MediaResource;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CuratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('curator')
            ->hasConfigFile()
            ->hasRoute('web')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_media_table')
            ->hasCommands([
                Commands\UpgradeCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->askToRunMigrations();
            });
    }

    public function packageBooted(): void
    {
        app()->bind(Media::class, config('curator.model'));
        app()->bind(MediaResource::class, config('curator.resources.resource'));

        app(Media::class)::observe(MediaObserver::class);

        Livewire::component('curator-panel', Components\Modals\CuratorPanel::class);
        Livewire::component('curator-curation', Components\Modals\CuratorCuration::class);

        Blade::component('curator-glider', View\Components\Glider::class);
        Blade::component('curator-curation', View\Components\Curation::class);

        FilamentAsset::register([
            AlpineComponent::make('curation', __DIR__ . '/../resources/dist/curation.js')->loadedOnRequest(),
            Css::make('curator', __DIR__ . '/../resources/dist/curator.css'),
        ], 'awcodes/curator');
    }
}
