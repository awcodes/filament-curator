<?php

declare(strict_types=1);

namespace Awcodes\Curator;

use Awcodes\Curator\Commands\GenerateGlideTokenCommand;
use Awcodes\Curator\Commands\InstallCommand;
use Awcodes\Curator\Components\Modals\CuratorCuration;
use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Config\CurationManager;
use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\MediaResource;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Awcodes\Curator\Resources\Media\Pages\EditMedia;
use Awcodes\Curator\Resources\Media\Pages\ListMedia;
use Awcodes\Curator\Resources\Media\Schemas\MediaForm;
use Awcodes\Curator\Resources\Media\Tables\MediaTable;
use Awcodes\Curator\View\Components\Curation;
use Awcodes\Curator\View\Components\Glider;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
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
            concrete: fn (): CuratorManager => new CuratorManager(),
        );

        $this->app->scoped(
            abstract: GlideManager::class,
            concrete: fn (): GlideManager => new GlideManager(),
        );

        $this->app->scoped(
            abstract: CurationManager::class,
            concrete: fn (): CurationManager => new CurationManager(),
        );

        $this->app->bind(
            abstract: Media::class,
            concrete: config('curator.model'),
        );

        $this->app->bind(
            abstract: MediaResource::class,
            concrete: config('curator.resource.resource'),
        );

        $this->app->bind(
            abstract: CreateMedia::class,
            concrete: config('curator.resource.pages.create'),
        );

        $this->app->bind(
            abstract: EditMedia::class,
            concrete: config('curator.resource.pages.edit'),
        );

        $this->app->bind(
            abstract: ListMedia::class,
            concrete: config('curator.resource.pages.index'),
        );

        $this->app->bind(
            abstract: MediaForm::class,
            concrete: config('curator.resource.schemas.form'),
        );

        $this->app->bind(
            abstract: MediaTable::class,
            concrete: config('curator.resource.tables.table'),
        );
    }

    public function packageBooted(): void
    {
        Livewire::component(name: 'curator-panel', class: CuratorPanel::class);
        Livewire::component(name: 'curator-curation', class: CuratorCuration::class);

        Blade::component(class: 'curator-glider', alias: Glider::class);
        Blade::component(class: 'curator-curation', alias: Curation::class);

        FilamentAsset::register([
            // CSS Assets
            Css::make(id: 'curator-styles', path: __DIR__.'/../resources/dist/curator.css'),

            // JavaScript Assets
            Js::make(id: 'curator-scripts', path: __DIR__.'/../resources/dist/curator.js'),
            AlpineComponent::make(id: 'curation', path: __DIR__.'/../resources/dist/curation.js'),
        ], package: 'awcodes/curator');
    }
}
