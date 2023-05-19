<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Commands\UpgradeCommand;
use Awcodes\Curator\Observers\MediaObserver;
use Composer\InstalledVersions;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;

class CuratorServiceProvider extends PluginServiceProvider
{
    public static string $name = 'curator';

    public static string $version = 'dev';

    public function configurePackage(Package $package): void
    {
        static::$version = InstalledVersions::getVersion('awcodes/filament-curator');

        $package->name(static::$name)
            ->hasRoute('web')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_media_table')
            ->hasCommands([
                UpgradeCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->askToRunMigrations();
            });
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton('curator', fn (): Curator => new Curator());
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        app('curator')->getMediaModel()::observe(MediaObserver::class);

        Livewire::component('curator-panel', Components\Modals\CuratorPanel::class);
        Livewire::component('curator-curation', Components\Modals\CuratorCuration::class);

        Blade::component('curator-glider', View\Components\Glider::class);
        Blade::component('curator-curation', View\Components\Curation::class);
    }

    protected function getResources(): array
    {
        return [
            Resources\MediaResource::class,
        ];
    }

    protected function getStyles(): array
    {
        return [
            'plugin-curator-'.static::$version => __DIR__.'/../resources/dist/curator.css',
        ];
    }

    protected function getBeforeCoreScripts(): array
    {
        return [
            'plugin-curator-'.static::$version => __DIR__.'/../resources/dist/curator.js',
        ];
    }
}
