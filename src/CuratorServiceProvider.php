<?php

declare(strict_types=1);

namespace Awcodes\Curator;

use Awcodes\Curator\Commands\GenerateGlideTokenCommand;
use Awcodes\Curator\Commands\InstallCommand;
use Awcodes\Curator\Commands\SanitizeSvgsCommand;
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
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CuratorServiceProvider extends PackageServiceProvider
{
    /**
     * The managers behind the `Curator`, `Glide` and `Curation` facades.
     *
     * @var array<class-string>
     */
    protected const MANAGERS = [
        CuratorManager::class,
        GlideManager::class,
        CurationManager::class,
    ];

    /**
     * The managers as the application's service providers configured them.
     *
     * @var array<class-string, object>
     */
    protected array $configuredManagers = [];

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
                SanitizeSvgsCommand::class,
            ]);
    }

    /**
     * Queue workers and Octane forget scoped instances between jobs and requests. Each new instance starts from a
     * copy of the managers as they were once the app booted, so configuration made in a service provider lasts,
     * while anything changed during one request or job is reset for the next.
     *
     * @internal
     */
    public function rememberConfiguredManagers(): void
    {
        foreach (self::MANAGERS as $manager) {
            $this->configuredManagers[$manager] = clone $this->app->make($manager);
        }
    }

    public function packageRegistered(): void
    {
        foreach (self::MANAGERS as $manager) {
            $this->app->scoped(
                abstract: $manager,
                concrete: fn (): object => isset($this->configuredManagers[$manager])
                    ? clone $this->configuredManagers[$manager]
                    : new $manager(),
            );
        }

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
        $this->app->booted(fn () => $this->rememberConfiguredManagers());

        Livewire::component(name: 'curator-panel', class: CuratorPanel::class);
        Livewire::component(name: 'curator-curation', class: CuratorCuration::class);

        Blade::component(class: 'curator-glider', alias: Glider::class);
        Blade::component(class: 'curator-curation', alias: Curation::class);

        FilamentAsset::register([
            AlpineComponent::make(id: 'curation', path: __DIR__ . '/../resources/dist/curation.js'),
            Js::make(id: 'rich-editor-integration', path: __DIR__ . '/../resources/dist/rich-editor-integration.js')->loadedOnRequest(),
        ], package: 'awcodes/curator');
    }
}
