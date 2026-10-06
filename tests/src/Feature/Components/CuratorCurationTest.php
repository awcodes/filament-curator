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

// A 200×100 image whose left half is red and right half blue, so the result shows which way it was turned.
function splitImageComponent(): \Livewire\Features\SupportTesting\Testable
{
    Storage::fake('public');

    $image = imagecreatetruecolor(200, 100);
    imagefilledrectangle($image, 0, 0, 99, 99, imagecolorallocate($image, 255, 0, 0));
    imagefilledrectangle($image, 100, 0, 199, 99, imagecolorallocate($image, 0, 0, 255));
    ob_start();
    imagepng($image);
    Storage::disk('public')->put('media/split.png', ob_get_clean());

    $media = makeMedia(['name' => 'split', 'directory' => 'media', 'path' => 'media/split.png', 'ext' => 'png', 'type' => 'image/png', 'width' => 200, 'height' => 100]);

    return Livewire::test(CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.image',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ]);
}

function savedPixel(string $path, int $x, int $y): string
{
    $image = imagecreatefromstring(Storage::disk('public')->get($path));
    ['red' => $red, 'blue' => $blue] = imagecolorsforindex($image, imagecolorat($image, $x, $y));

    return $red > $blue ? 'red' : 'blue';
}

function splitPayload(array $overrides = []): array
{
    return curationPayload(array_replace([
        'key' => 'custom-crop',
        'format' => 'png',
        'width' => 200,
        'height' => 100,
        'canvasData' => ['width' => 500, 'height' => 250, 'naturalWidth' => 200, 'naturalHeight' => 100],
    ], $overrides));
}

test('a positive rotation turns the image clockwise, as the cropper shows it', function () {
    splitImageComponent()->call('saveCuration', splitPayload(['rotate' => 90, 'width' => 100, 'height' => 200]));

    // Turned clockwise, the red left half ends up on top.
    expect(savedPixel('media/split/custom-crop.png', 50, 20))->toBe('red')
        ->and(savedPixel('media/split/custom-crop.png', 50, 180))->toBe('blue');
});

test('flipping horizontally mirrors left and right', function () {
    splitImageComponent()->call('saveCuration', splitPayload(['scaleX' => -1]));

    expect(savedPixel('media/split/custom-crop.png', 20, 50))->toBe('blue')
        ->and(savedPixel('media/split/custom-crop.png', 180, 50))->toBe('red');
});

test('flipping vertically leaves left and right in place', function () {
    splitImageComponent()->call('saveCuration', splitPayload(['scaleY' => -1.0]));

    expect(savedPixel('media/split/custom-crop.png', 20, 50))->toBe('red');
});

test('a custom curation keeps the crop size of the original image, whatever the cropper size on screen', function () {
    splitImageComponent()->call('saveCuration', splitPayload(['width' => 120, 'height' => 80]));

    [$width, $height] = getimagesizefromstring(Storage::disk('public')->get('media/split/custom-crop.png'));

    expect([$width, $height])->toBe([120, 80]);
});

test('a registered preset is saved at the preset size', function () {
    Awcodes\Curator\Facades\Curation::presets([
        Awcodes\Curator\Curations\CurationPreset::make('Banner')->width(160)->height(40)->format('png'),
    ]);

    splitImageComponent()->call('saveCuration', splitPayload(['key' => 'banner', 'width' => 200, 'height' => 50]));

    [$width, $height] = getimagesizefromstring(Storage::disk('public')->get('media/split/banner.png'));

    expect([$width, $height])->toBe([160, 40]);
});

test('the default curation formats are ones a curation can be saved in', function () {
    expect(config('curator.curation_formats'))
        ->toBe(Awcodes\Curator\Enums\CurationFormats::toArray());
});

test('the editor drops formats a curation cannot be saved in', function () {
    config(['curator.curation_formats' => ['jpg', 'gif', 'svg', 'webp', 'bmp']]);

    expect(Awcodes\Curator\Components\Forms\CuratorEditor::make('curation')->getFormats())->toBe(['jpg', 'webp']);
});
