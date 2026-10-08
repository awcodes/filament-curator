<?php

use Awcodes\Curator\Components\Modals\CuratorCuration;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaRecordDeniedPolicy;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

function curatedMedia(int $width = 100, int $height = 100): Media
{
    $file = UploadedFile::fake()->image('source.jpg', $width, $height);
    Storage::disk('public')->put('media/source.jpg', $file->getContent());

    return Media::factory()->create([
        'disk' => 'public',
        'directory' => 'media',
        'name' => 'source',
        'path' => 'media/source.jpg',
        'ext' => 'jpg',
        'type' => 'image/jpeg',
        'width' => $width,
        'height' => $height,
    ]);
}

function cropPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'key' => 'custom-crop',
        'format' => 'jpg',
        'quality' => 60,
        'width' => 50,
        'height' => 50,
        'x' => 0,
        'y' => 0,
        'rotate' => 0,
        'scaleX' => 1,
        'scaleY' => 1,
        'canvasData' => ['width' => 100, 'height' => 100, 'naturalWidth' => 100, 'naturalHeight' => 100],
    ], $overrides);
}

function curate(Media $media, array $payload)
{
    return Livewire::test(CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.curations.0.curation',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ])->call('saveCuration', $payload);
}

function storedCurationSize(Media $media, string $key, string $extension = 'jpg'): array
{
    $info = getimagesizefromstring(Storage::disk('public')->get("media/source/{$key}.{$extension}"));

    return [$info[0], $info[1]];
}

beforeEach(function () {
    Storage::fake('public');
});

test('a crop within the image is saved at the size it was shown at', function () {
    $media = curatedMedia();

    curate($media, cropPayload([
        'x' => 10, 'y' => 10, 'width' => 50, 'height' => 40,
        'canvasData' => ['width' => 200, 'height' => 200],
    ]))->assertDispatched('add-curation', fn (string $name, array $params): bool => $params['curation']['width'] === 100 && $params['curation']['height'] === 80);

    expect(storedCurationSize($media, 'custom-crop'))->toBe([100, 80]);
});

test('a custom crop past the image is trimmed to the image', function () {
    $media = curatedMedia();

    curate($media, cropPayload(['width' => 3000, 'height' => 3000]))
        ->assertDispatched('add-curation', fn (string $name, array $params): bool => $params['curation']['width'] === 100 && $params['curation']['height'] === 100);

    expect(storedCurationSize($media, 'custom-crop'))->toBe([100, 100]);
});

test('a custom crop is scaled down to the maximum dimension', function () {
    config()->set('curator.curation_max_dimension', 300);

    $media = curatedMedia();

    curate($media, cropPayload([
        'width' => 100, 'height' => 50,
        'canvasData' => ['width' => 65535, 'height' => 65535],
    ]));

    expect(storedCurationSize($media, 'custom-crop'))->toBe([300, 150]);
});

test('a preset crop past the image keeps its shape within the maximum dimension', function () {
    config()->set('curator.curation_max_dimension', 400);

    $media = curatedMedia();

    curate($media, cropPayload(['key' => 'thumbnail', 'width' => 3000, 'height' => 1500]));

    expect(storedCurationSize($media, 'thumbnail'))->toBe([400, 200]);
});

test('a preset crop that overhangs the image is padded to the box shape', function () {
    $media = curatedMedia(100, 50);

    curate($media, cropPayload(['key' => 'thumbnail', 'x' => 0, 'y' => -25, 'width' => 100, 'height' => 100]));

    expect(storedCurationSize($media, 'thumbnail'))->toBe([100, 100]);
});

test('a crop that misses the image is rejected', function () {
    $media = curatedMedia();

    curate($media, cropPayload(['x' => 500, 'y' => 500]))
        ->assertHasErrors('width')
        ->assertNotDispatched('add-curation');

    expect(Storage::disk('public')->exists('media/source/custom-crop.jpg'))->toBeFalse();
});

test('out of range crop values are rejected', function (array $overrides, string $field) {
    $media = curatedMedia();

    curate($media, cropPayload($overrides))
        ->assertHasErrors($field)
        ->assertNotDispatched('add-curation');
})->with([
    [['width' => 70000], 'width'],
    [['height' => 70000], 'height'],
    [['x' => -70000], 'x'],
    [['y' => 70000], 'y'],
    [['rotate' => 720], 'rotate'],
    [['scaleX' => 40], 'scaleX'],
    [['scaleY' => 0.5], 'scaleY'],
    [['canvasData' => ['width' => 1000000]], 'canvasData.width'],
    [['canvasData' => ['naturalHeight' => 1000000]], 'canvasData.naturalHeight'],
]);

test('saving a curation requires the update ability on the media', function () {
    Gate::policy(Media::class, MediaRecordDeniedPolicy::class);

    $media = curatedMedia();

    curate($media, cropPayload())->assertForbidden();

    expect(Storage::disk('public')->exists('media/source/custom-crop.jpg'))->toBeFalse();
});

test('the media cannot be changed from the browser', function () {
    $media = curatedMedia();
    $other = Media::factory()->create();

    Livewire::test(CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.curations.0.curation',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ])->set('media', $other);
})->throws(CannotUpdateLockedPropertyException::class);
