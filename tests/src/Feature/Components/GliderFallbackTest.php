<?php

declare(strict_types=1);

use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GliderFallback;
use Awcodes\Curator\View\Components\Glider;

function registerThumbnailFallback(): GliderFallback
{
    $fallback = GliderFallback::make('thumbnail')
        ->alt('Placeholder')
        ->height(256)
        ->width(256)
        ->source('fallback.jpg')
        ->type('jpg');

    Glide::registerGliderFallbacks([$fallback]);

    return $fallback;
}

test('make sets the name on the fallback', function () {
    expect(GliderFallback::make('default')->getName())->toBe('default');
});

test('a fallback registers under the name given to make', function () {
    registerThumbnailFallback();

    expect(Glide::getGliderFallback('thumbnail'))->toBeInstanceOf(GliderFallback::class)
        ->and(Glide::getGliderFallbacks())->toHaveKey('thumbnail');
});

test('optional getters return null instead of throwing when unset', function () {
    $fallback = GliderFallback::make('bare');

    expect($fallback->getAlt())->toBeNull()
        ->and($fallback->getHeight())->toBeNull()
        ->and($fallback->getWidth())->toBeNull()
        ->and($fallback->getSource())->toBeNull()
        ->and($fallback->getType())->toBeNull()
        ->and($fallback->isResizable())->toBeFalse()
        ->and($fallback->isPreviewable())->toBeFalse();
});

test('isPreviewable reports svg sources as previewable but not resizable', function () {
    $fallback = GliderFallback::make('vector')->source('logo.svg');

    expect($fallback->isPreviewable())->toBeTrue()
        ->and($fallback->isResizable())->toBeFalse();
});

test('the glider component accepts a null media item when a fallback is set', function () {
    registerThumbnailFallback();

    $glider = new Glider(media: null, fallback: 'thumbnail');

    expect($glider->mediaItem->getPath())->toBe('fallback.jpg')
        ->and($glider->mediaItem->getAlt())->toBe('Placeholder')
        ->and($glider->mediaItem->getWidth())->toBe(256);
});

test('the glider component falls back when an id does not resolve', function () {
    registerThumbnailFallback();

    $glider = new Glider(media: 999, fallback: 'thumbnail');

    expect($glider->mediaItem->getPath())->toBe('fallback.jpg');
});

test('the glider component falls back on a blank string media item', function () {
    registerThumbnailFallback();

    $glider = new Glider(media: '', fallback: 'thumbnail');

    expect($glider->mediaItem->getPath())->toBe('fallback.jpg');
});

test('an existing media record still wins over the fallback', function () {
    registerThumbnailFallback();

    $media = makeMedia(['path' => 'real.jpg']);

    $glider = new Glider(media: $media->id, fallback: 'thumbnail');

    expect($glider->mediaItem->getPath())->toBe('real.jpg');
});

test('a null media item without a fallback still throws', function () {
    new Glider(media: null);
})->throws(Exception::class, 'Invalid media item provided to Glider component.');

test('an unregistered fallback name throws rather than dereferencing null', function () {
    new Glider(media: null, fallback: 'does-not-exist');
})->throws(Exception::class, 'Invalid media item provided to Glider component.');
