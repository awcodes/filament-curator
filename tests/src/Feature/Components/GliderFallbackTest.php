<?php

declare(strict_types=1);

use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GliderFallback;
use Awcodes\Curator\View\Components\Glider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;

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

test('the setters accept null so a conditional value does not throw', function () {
    $fallback = GliderFallback::make('conditional')
        ->alt(null)
        ->height(null)
        ->source(null)
        ->type(null)
        ->width(null);

    expect($fallback->getSource())->toBeNull()
        ->and($fallback->getAlt())->toBeNull();
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

test('a null media item without a fallback renders nothing', function () {
    $glider = new Glider(media: null);

    expect($glider->shouldRender())->toBeFalse()
        ->and(Blade::render('<x-curator-glider :media="null" />'))->toBe('');
});

// Deleting one media item used to 500 every page that rendered it.
test('an id that no longer resolves renders nothing and logs a warning', function () {
    Log::spy();

    $html = Blade::render('<x-curator-glider :media="$media" />', ['media' => 999]);

    expect($html)->toBe('');

    Log::shouldHaveReceived('warning')->once()->withArgs(fn (string $message): bool => str_contains($message, '[999]'));
});

test('an unregistered fallback name is reported as a configuration mistake', function () {
    new Glider(media: null, fallback: 'does-not-exist');
})->throws(Exception::class, 'The [does-not-exist] glider fallback is not registered.');

test('a registered fallback without a source names itself in the exception', function () {
    Glide::registerGliderFallbacks([GliderFallback::make('logo')->source(null)]);

    new Glider(media: null, fallback: 'logo');
})->throws(Exception::class, 'The [logo] glider fallback does not have a source.');
