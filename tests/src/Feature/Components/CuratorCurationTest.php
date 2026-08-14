<?php

use Awcodes\Curator\Components\Modals\CuratorCuration;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

function curationPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'key' => 'thumbnail',
        'format' => 'jpg',
        'quality' => 60,
        'width' => 50,
        'height' => 50,
        'x' => 0,
        'y' => 0,
        'rotate' => 0,
        'scaleX' => 1,
        'scaleY' => 1,
        'canvasData' => [
            'width' => 100,
            'height' => 100,
            'naturalWidth' => 100,
            'naturalHeight' => 100,
        ],
    ], $overrides);
}

/**
 * Exercises the rules on their own. saveCuration() goes on to hand the media to
 * Intervention, which would fetch the faked storage URL over HTTP, so the valid
 * payload is checked here rather than through a full round trip.
 */
function validateCurationPayload(array $payload): array
{
    $method = new ReflectionMethod(CuratorCuration::class, 'validateCuration');
    $method->setAccessible(true);

    return $method->invoke(new CuratorCuration, $payload);
}

test('a valid payload passes validation', function () {
    expect(validateCurationPayload(curationPayload()))
        ->toHaveKey('key', 'thumbnail');
});

test('keys with spaces and dots are still accepted', function () {
    expect(validateCurationPayload(curationPayload(['key' => 'Hero Banner 2.0'])))
        ->toHaveKey('key', 'Hero Banner 2.0');
});

test('a key containing a path separator is rejected', function () {
    validateCurationPayload(curationPayload(['key' => '../../escaped']));
})->throws(ValidationException::class);

test('an unsupported format is rejected', function () {
    validateCurationPayload(curationPayload(['format' => 'php']));
})->throws(ValidationException::class);

test('a zero natural width is rejected rather than dividing by zero', function () {
    validateCurationPayload(curationPayload(['canvasData' => ['naturalWidth' => 0]]));
})->throws(ValidationException::class);

test('a missing payload is rejected', function () {
    validateCurationPayload([]);
})->throws(ValidationException::class);

test('saveCuration refuses a traversing key without writing a file', function () {
    Storage::fake('public');

    $media = Media::factory()->create();

    Livewire::test(CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.image',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ])
        ->call('saveCuration', curationPayload(['key' => '../../escaped']))
        ->assertHasErrors('key')
        ->assertNotDispatched('add-curation');

    // Flysystem only refuses traversal that leaves the disk root, so without
    // validation this would have landed above the media's own directory.
    expect(Storage::disk($media->disk)->exists('escaped.jpg'))->toBeFalse();
});
