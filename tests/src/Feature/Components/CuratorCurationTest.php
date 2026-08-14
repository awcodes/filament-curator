<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorCuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

function curationComponent(): \Livewire\Features\SupportTesting\Testable
{
    Storage::fake('public');
    Storage::disk('public')->put(
        'media/photo.jpg',
        UploadedFile::fake()->image('photo.jpg', 100, 100)->getContent(),
    );

    $media = makeMedia([
        'name' => 'photo',
        'directory' => 'media',
        'path' => 'media/photo.jpg',
    ]);

    return Livewire::test(CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.image',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ]);
}

test('a valid curation is saved', function () {
    curationComponent()
        ->call('saveCuration', curationPayload())
        ->assertHasNoErrors()
        ->assertDispatched('add-curation');

    expect(Storage::disk('public')->exists('media/photo/thumbnail.jpg'))->toBeTrue();
});

test('a key containing a path separator is rejected', function () {
    curationComponent()
        ->call('saveCuration', curationPayload(['key' => '../../escaped']))
        ->assertHasErrors('key')
        ->assertNotDispatched('add-curation');

    // Flysystem only refuses traversal that leaves the disk root, so without
    // validation this would have landed as a sibling of the media directory.
    expect(Storage::disk('public')->exists('escaped.jpg'))->toBeFalse();
});

test('a key that walks up a single level cannot overwrite the source media', function () {
    $component = curationComponent();
    $original = Storage::disk('public')->get('media/photo.jpg');

    // The curation is written to `{directory}/{name}/{key}.{ext}`, so `../photo`
    // resolves back onto the original file the curation was cropped from.
    $component
        ->call('saveCuration', curationPayload(['key' => '../photo']))
        ->assertHasErrors('key');

    expect(Storage::disk('public')->get('media/photo.jpg'))->toBe($original);
});

test('keys with spaces and dots are still accepted', function () {
    curationComponent()
        ->call('saveCuration', curationPayload(['key' => 'Hero Banner 2.0']))
        ->assertHasNoErrors();

    expect(Storage::disk('public')->exists('media/photo/Hero Banner 2.0.jpg'))->toBeTrue();
});

test('an unsupported format is rejected', function () {
    curationComponent()
        ->call('saveCuration', curationPayload(['format' => 'php']))
        ->assertHasErrors('format')
        ->assertNotDispatched('add-curation');
});

test('a zero natural width is rejected rather than dividing by zero', function () {
    curationComponent()
        ->call('saveCuration', curationPayload(['canvasData' => ['naturalWidth' => 0]]))
        ->assertHasErrors('canvasData.naturalWidth');
});

test('a missing payload is rejected', function () {
    curationComponent()
        ->call('saveCuration')
        ->assertHasErrors(['key', 'width', 'height']);
});

test('an out of range quality is rejected', function () {
    curationComponent()
        ->call('saveCuration', curationPayload(['quality' => 5000]))
        ->assertHasErrors('quality');
});
