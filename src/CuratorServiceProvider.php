<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Commands\UpgradeCommand;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Observers\MediaObserver;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;

class CuratorServiceProvider extends PluginServiceProvider
{
    public static string $name = 'curator';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasRoute('web')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_media_table')
            ->hasCommands([
                UpgradeCommand::class
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

        Media::observe(MediaObserver::class);

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
            'plugin-curator' => __DIR__.'/../resources/dist/curator.css',
        ];
    }

    protected function getBeforeCoreScripts(): array
    {
        return [
            'plugin-curator' => __DIR__.'/../resources/dist/curator.js',
        ];
    }
}
