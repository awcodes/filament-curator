<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\CuratorServiceProvider;
use Awcodes\Curator\Facades\Curation;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GliderFallback;
use Awcodes\Curator\Glide\SymfonyResponseFactory;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Storage;

// Queue workers and Octane forget scoped instances between jobs and requests. Settings made while the app boots,
// in a service provider, have to survive that; settings changed during one request must not leak into the next.
function forgetBetweenRequests(): void
{
    app()->forgetScopedInstances();
    Facade::clearResolvedInstances();
}

test('settings made while the app boots survive the container forgetting scoped instances', function () {
    Curator::disk('s3')->maxSize(42);
    Glide::basePath('images');
    Curation::presets([CurationPreset::make('Hero')->width(1200)->height(600)]);

    app()->getProvider(CuratorServiceProvider::class)->rememberConfiguredManagers();

    forgetBetweenRequests();

    expect(app(CuratorManager::class)->getDiskName())->toBe('s3')
        ->and(Curator::getMaxSize())->toBe(42)
        ->and(app(GlideManager::class)->getBasePath())->toBe('images')
        ->and(collect(Curation::getPresets())->map->getKey()->all())->toContain('hero');
});

test('settings changed during a request are reset for the next one', function () {
    Curator::disk('s3');

    app()->getProvider(CuratorServiceProvider::class)->rememberConfiguredManagers();

    Curator::directory('tenant-a');

    forgetBetweenRequests();

    expect(app(CuratorManager::class)->getDirectory())->toBe(config('curator.default_directory'))
        ->and(app(CuratorManager::class)->getDiskName())->toBe('s3');
});

test('a picker uses the facade settings it does not set itself', function () {
    Curator::acceptedFileTypes(['image/png'])->disk('s3')->directory('uploads')->visibility('private')->maxSize(42)->minSize(2);

    $picker = CuratorPicker::make('media');

    expect($picker->getAcceptedFileTypes())->toBe(['image/png'])
        ->and($picker->getDiskName())->toBe('s3')
        ->and($picker->getDirectory())->toBe('uploads')
        ->and($picker->getVisibility())->toBe('private')
        ->and($picker->getMaxSize())->toBe(42)
        ->and($picker->getMinSize())->toBe(2);
});

test('a picker setting takes precedence over the facade', function () {
    Curator::disk('s3')->maxSize(42);

    $picker = CuratorPicker::make('media')->disk('public')->maxSize(100);

    expect($picker->getDiskName())->toBe('public')
        ->and($picker->getMaxSize())->toBe(100);
});

test('a picker without facade settings keeps the config defaults', function () {
    $picker = CuratorPicker::make('media');

    expect($picker->getDiskName())->toBe(config('curator.default_disk'))
        ->and($picker->getMaxSize())->toBe(5000)
        ->and($picker->getMinSize())->toBe(0);
});

test('a picker does not inherit the facade filename setting', function () {
    Curator::preserveFilenames();

    expect(CuratorPicker::make('media')->shouldPreserveFilenames())->toBeFalse();
});

test('a custom server config without a response factory gets one for the current request', function () {
    Glide::serverConfig([
        'source' => Storage::disk('public')->getDriver(),
        'cache' => storage_path('app'),
        'cache_path_prefix' => '.cache',
    ]);

    expect(Glide::getServer()->getResponseFactory())->toBeInstanceOf(SymfonyResponseFactory::class);
});

test('a request cannot change the fallbacks and presets the next one starts from', function () {
    Glide::registerGliderFallbacks([GliderFallback::make('thumbnail')->source('/images/placeholder.jpg')]);
    Curation::presets([CurationPreset::make('Hero')->width(1200)->height(600)]);

    app()->getProvider(CuratorServiceProvider::class)->rememberConfiguredManagers();
    forgetBetweenRequests();

    Glide::getGliderFallback('thumbnail')->source('/changed.jpg');
    Curation::getPresets()[0]->width(7);

    forgetBetweenRequests();

    expect(Glide::getGliderFallback('thumbnail')->getSource())->toBe('/images/placeholder.jpg')
        ->and(Curation::getPresets()[0]->getWidth())->toBe(1200);
});
