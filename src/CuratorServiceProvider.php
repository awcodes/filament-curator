<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Commands\GenerateGlideTokenCommand;
use Awcodes\Curator\Commands\InstallCommand;
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
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CuratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name(name: 'curator')
            ->hasConfigFile()
            ->hasRoute(routeFileName: 'curator')
            ->hasViews()
            ->hasTranslations()
            ->hasCommands([
                InstallCommand::class,
                GenerateGlideTokenCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(
            abstract: CuratorManager::class,
            concrete: fn () => new CuratorManager(),
        );

        $this->app->scoped(
            abstract: GlideManager::class,
            concrete: fn () => new GlideManager(),
        );

        $this->app->scoped(
            abstract: CurationManager::class,
            concrete: fn () => new CurationManager(),
        );
    }

    public function packageBooted(): void
    {
        $this->app->bind(
            abstract: Media::class,
            concrete: config('curator.model'),
        );

        $this->app->bind(
            abstract: MediaResource::class,
            concrete: config('curator.resource.resource'),
        );

        $this->app->bind(
            abstract: MediaResource\CreateMedia::class,
            concrete: config('curator.resource.pages.create'),
        );

        $this->app->bind(
            abstract: MediaResource\EditMedia::class,
            concrete: config('curator.resource.pages.edit'),
        );

        $this->app->bind(
            abstract: MediaResource\ListMedia::class,
            concrete: config('curator.resource.pages.index'),
        );

        app(abstract: Media::class)::observe(classes: MediaObserver::class);

        Livewire::component(name: 'curator-panel', class: Components\Modals\CuratorPanel::class);
        Livewire::component(name: 'curator-curation', class: Components\Modals\CuratorCuration::class);

        Blade::component(class: 'curator-glider', alias: Glider::class);
        Blade::component(class: 'curator-curation', alias: Curation::class);

        FilamentAsset::register([
            AlpineComponent::make(id: 'curation', path: __DIR__ . '/../resources/dist/curation.js'),
            Css::make(id: 'curator', path: __DIR__ . '/../resources/dist/curator.css')->loadedOnRequest(),
        ], package: 'awcodes/curator');
    }
}
