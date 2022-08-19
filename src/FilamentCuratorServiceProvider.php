<?php

namespace FilamentCurator;

use Livewire\Livewire;
use Filament\Facades\Filament;
use FilamentCurator\Observers\MediaObserver;
use Filament\PluginServiceProvider;
use Illuminate\Support\Facades\File;
use Filament\Navigation\UserMenuItem;
use FilamentCurator\Commands\InstallCommand;
use FilamentCurator\Commands\MakeInstallCuratorCommand;
use FilamentCurator\Commands\MakePublishCuratorCommand;
use Spatie\LaravelPackageTools\Package;
use FilamentCurator\Commands\RegenerateThumbnails;

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
            ->hasRoute("web")
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

        Livewire::component('filament-curator-media-picker-modal', Forms\Components\MediaPickerModal::class);
        Livewire::component('filament-curator-create-media-form', Forms\Components\CreateMediaForm::class);
    }
}
