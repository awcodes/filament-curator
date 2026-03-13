<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;

test('pretty_name falls back to name.ext when no title', function () {
    $media = new Media(['name' => 'my-photo', 'ext' => 'jpg', 'title' => null]);

    expect($media->pretty_name)->toBe('my-photo.jpg');
});

test('pretty_name returns title when set', function () {
    $media = new Media(['name' => 'my-photo', 'ext' => 'jpg', 'title' => 'My Photo Title']);

    expect($media->pretty_name)->toBe('My Photo Title');
});

test('getPrettyName mirrors the pretty_name attribute logic', function () {
    $media = new Media(['name' => 'file', 'ext' => 'png', 'title' => null]);

    expect($media->getPrettyName())->toBe('file.png');

    $media->title = 'A Title';

    expect($media->getPrettyName())->toBe('A Title');
});

test('getCuration returns the matching curation array', function () {
    $media = new Media([
        'curations' => [
            ['curation' => ['key' => 'hero', 'width' => 1920, 'height' => 1080]],
            ['curation' => ['key' => 'thumb', 'width' => 150, 'height' => 150]],
        ],
    ]);

    expect($media->getCuration('hero'))->toBe(['key' => 'hero', 'width' => 1920, 'height' => 1080]);
});

test('getCuration returns empty array for a missing key', function () {
    $media = new Media(['curations' => []]);

    expect($media->getCuration('missing'))->toBe([]);
});

test('hasCuration returns true when the curation exists', function () {
    $media = new Media([
        'curations' => [
            ['curation' => ['key' => 'thumb', 'width' => 150, 'height' => 150]],
        ],
    ]);

    expect($media->hasCuration('thumb'))->toBeTrue();
});

test('hasCuration returns false when the curation does not exist', function () {
    $media = new Media(['curations' => []]);

    expect($media->hasCuration('missing'))->toBeFalse();
});

test('url returns a string URL for a public file', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/test.jpg', 'fake-content');

    $media = new Media([
        'disk' => 'public',
        'path' => 'media/test.jpg',
        'visibility' => 'public',
    ]);

    expect($media->url)->toBeString()->toContain('media/test.jpg');
});

test('full_path returns the absolute filesystem path', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/image.png', 'fake-content');

    $media = new Media([
        'disk' => 'public',
        'path' => 'media/image.png',
    ]);

    expect($media->full_path)->toBeString()->toContain('media/image.png');
});
