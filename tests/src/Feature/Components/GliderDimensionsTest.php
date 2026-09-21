<?php

declare(strict_types=1);

use Awcodes\Curator\View\Components\Glider;
use Illuminate\Support\Facades\Blade;

// Glide scales the missing side with the requested one, so writing the
// original's height beside a requested width distorted the box and shifted the
// layout once the real image arrived.

function landscapeMedia(): Awcodes\Curator\Models\Media
{
    return makeMedia(['path' => 'landscape.jpg', 'width' => 1600, 'height' => 900]);
}

test('a requested width derives the height from the aspect ratio', function () {
    $html = Blade::render('<x-curator-glider :media="$media" width="960" />', ['media' => landscapeMedia()]);

    expect($html)->toContain('width="960"')
        ->and($html)->toContain('height="540"');
});

test('a requested height derives the width from the aspect ratio', function () {
    $html = Blade::render('<x-curator-glider :media="$media" height="450" />', ['media' => landscapeMedia()]);

    expect($html)->toContain('width="800"')
        ->and($html)->toContain('height="450"');
});

test('both requested sides are used as given', function () {
    $glider = new Glider(media: landscapeMedia(), width: '300', height: '300');

    expect($glider->resolveDisplayDimensions())->toBe([300, 300]);
});

test('without a requested size the original dimensions are used', function () {
    $glider = new Glider(media: landscapeMedia());

    expect($glider->resolveDisplayDimensions())->toBe([1600, 900]);
});

test('a non-numeric side is ignored when deriving the other', function () {
    $glider = new Glider(media: landscapeMedia(), width: '960', height: 'auto');

    expect($glider->resolveDisplayDimensions())->toBe([960, 540]);
});

test('media without known dimensions omits the side it cannot derive', function () {
    $html = Blade::render('<x-curator-glider media="images/banner.jpg" width="960" />');

    expect($html)->toContain('width="960"')
        ->and($html)->not->toContain('height=');
});

test('a srcset on media without known dimensions does not divide by zero', function () {
    $html = Blade::render(
        '<x-curator-glider media="images/banner.jpg" :srcset="[\'640w\']" sizes="100vw" />',
    );

    expect($html)->toContain('640w');
});
