<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Observers\MediaObserver;
use Awcodes\Curator\Resources\MediaResource;
use Awcodes\Curator\View\Components\Glider;
use Awcodes\Curator\View\Components\Curation;
use Filament\Facades\Filament;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
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

        Blade::component('curator-glider', Glider::class);
        Blade::component('curator-curation', Curation::class);

        FilamentAsset::register([
            AlpineComponent::make('curation', __DIR__ . '/../resources/dist/curation.js'),
            Css::make('curator', __DIR__ . '/../resources/dist/curator.css')->loadedOnRequest(),
        ], 'awcodes/curator');
    }
}
