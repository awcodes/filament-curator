<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Facades\Curation;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\SymfonyResponseFactory;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Storage;

// Queue workers and Octane forget scoped instances between jobs and requests. Facade settings are made once, in a
// service provider, so they have to survive that.
test('facade settings survive the container forgetting scoped instances', function () {
    Curator::disk('s3')->maxSize(42);
    Glide::basePath('images');
    Curation::presets([CurationPreset::make('Hero')->width(1200)->height(600)]);

    app()->forgetScopedInstances();
    Facade::clearResolvedInstances();

    expect(app(CuratorManager::class)->getDiskName())->toBe('s3')
        ->and(Curator::getMaxSize())->toBe(42)
        ->and(app(GlideManager::class)->getBasePath())->toBe('images')
        ->and(collect(Curation::getPresets())->map->getKey()->all())->toContain('hero');
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
