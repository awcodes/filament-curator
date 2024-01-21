<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Commands\GenerateGlideTokenCommand;
use Awcodes\Curator\Config\CurationManager;
use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Observers\MediaObserver;
use Awcodes\Curator\Resources\MediaResource;
use Awcodes\Curator\View\Components\Curation;
use Awcodes\Curator\View\Components\Glider;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
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
            ->hasMigration('create_curator_table')
            ->hasCommands([
                GenerateGlideTokenCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function (InstallCommand $command) {
                        $command->call('curator:token');
                    });
            });
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(
            CuratorManager::class,
            fn () => new CuratorManager(),
        );

        $this->app->scoped(
            GlideManager::class,
            fn () => new GlideManager(),
        );

        $this->app->scoped(
            CurationManager::class,
            fn () => new CurationManager(),
        );
    }

    public function packageBooted(): void
    {
        $this->app->bind(
            Media::class,
            config('curator.model'),
        );

        $this->app->bind(
            MediaResource::class,
            config('curator.resource.resource'),
        );

        $this->app->bind(
            MediaResource\CreateMedia::class,
            config('curator.resource.pages.create'),
        );

        $this->app->bind(
            MediaResource\EditMedia::class,
            config('curator.resource.pages.edit'),
        );

        $this->app->bind(
            MediaResource\ListMedia::class,
            config('curator.resource.pages.index'),
        );


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
