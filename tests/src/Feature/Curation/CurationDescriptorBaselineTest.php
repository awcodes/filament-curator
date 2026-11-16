<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorCuration;
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Facades\Curation;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\EditMedia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

// Characterization tests for the curation descriptor CuratorCuration::saveCuration()
// dispatches, the shape it ends up in on the media's `curations` column, and how
// rows written by earlier releases are still read. Expected values are literals
// captured from 5.x.
//
// quadrants.png is 40x20 with four flat 20x10 quadrants: red top left, green top
// right, blue bottom left, yellow bottom right.

beforeEach(function () {
    $this->storagePath = sys_get_temp_dir() . '/curator-curation-descriptor-baseline-' . Str::random(12);
    File::ensureDirectoryExists($this->storagePath . '/app');
    $this->app->useStoragePath($this->storagePath);

    Storage::fake('public');
    Storage::fake('local');

    config()->set('curator.glide_token', 'baseline-token');
    config()->set('curator.default_disk', 'public');
    config()->set('curator.temporary_url_expiration', 5);

    $this->travelTo(Carbon::parse('2026-01-01 12:00:00', 'UTC'));
});

afterEach(function () {
    File::deleteDirectory($this->storagePath);
});

function curationDescriptorBaselineMedia(array $overrides = []): Media
{
    $attributes = array_merge([
        'disk' => 'public',
        'directory' => 'media',
        'visibility' => 'public',
        'name' => 'quadrants',
        'path' => 'media/quadrants.png',
        'type' => 'image/png',
        'ext' => 'png',
        'width' => 40,
        'height' => 20,
    ], $overrides);

    Storage::disk($attributes['disk'])->put(
        $attributes['path'],
        File::get(__DIR__ . '/../../Fixtures/Files/glide/quadrants.png'),
    );

    return makeMedia($attributes);
}

function curationDescriptorBaselineComponent(Media $media): Testable
{
    return Livewire::test(CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.curations.item.curation',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ]);
}

function curationDescriptorBaselinePayload(array $overrides = []): array
{
    return array_replace([
        'key' => 'hero',
        'format' => 'png',
        'quality' => 60,
        'width' => 20,
        'height' => 10,
        'x' => 10,
        'y' => 5,
        'rotate' => 0,
        'scaleX' => 1,
        'scaleY' => 1,
        'canvasData' => ['width' => 400, 'height' => 200, 'naturalWidth' => 40, 'naturalHeight' => 20],
    ], $overrides);
}

/**
 * Saves a curation and returns the descriptor dispatched with `add-curation`.
 */
function curationDescriptorBaselineSave(Media $media, array $overrides = []): array
{
    $dispatched = null;

    curationDescriptorBaselineComponent($media)
        ->call('saveCuration', curationDescriptorBaselinePayload($overrides))
        ->assertHasNoErrors()
        ->assertDispatched('add-curation', function (string $name, array $params) use (&$dispatched): bool {
            $dispatched = $params;

            return true;
        });

    expect($dispatched)->toHaveKeys(['statePath', 'curation'])
        ->and($dispatched['statePath'])->toBe('data.curations.item.curation');

    return $dispatched['curation'];
}

/**
 * A curation row as releases before 5.5.0 saved it: the same twelve keys, with
 * width and height from floor() (whole floats, so plain integers once encoded)
 * and the url the disk gave at save time, here an absolute one from a host the
 * app no longer runs on.
 */
const CURATION_DESCRIPTOR_BASELINE_LEGACY_ROW = '[{"curation":{"key":"thumbnail","disk":"public","directory":"quadrants","visibility":"public","name":"thumbnail.webp","path":"media\/quadrants\/thumbnail.webp","width":200,"height":100,"size":1234,"type":"image\/webp","ext":"webp","url":"https:\/\/old.example.com\/storage\/media\/quadrants\/thumbnail.webp"}}]';

function curationDescriptorBaselineLegacyMedia(string $row = CURATION_DESCRIPTOR_BASELINE_LEGACY_ROW, array $overrides = []): Media
{
    $media = curationDescriptorBaselineMedia(array_merge(['alt' => 'Quadrants'], $overrides));

    DB::table($media->getTable())->where('id', $media->id)->update(['curations' => $row]);

    return $media->refresh();
}

test('a custom curation dispatches a twelve key descriptor in a fixed order', function () {
    $media = curationDescriptorBaselineMedia();

    $curation = curationDescriptorBaselineSave($media);

    // The encoded size depends on the zlib build, so it is read back from the disk rather than written out.
    expect($curation)->toBe([
        'key' => 'hero',
        'disk' => 'public',
        'directory' => 'quadrants',
        'visibility' => 'public',
        'name' => 'hero.png',
        'path' => 'media/quadrants/hero.png',
        'width' => 20,
        'height' => 10,
        'size' => Storage::disk('public')->size('media/quadrants/hero.png'),
        'type' => 'image/png',
        'ext' => 'png',
        'url' => '/storage/media/quadrants/hero.png',
    ])
        ->and(getimagesizefromstring(Storage::disk('public')->get('media/quadrants/hero.png')))->toMatchArray([0 => 20, 1 => 10, 'mime' => 'image/png']);
});

test('the descriptor directory is the media name, not the media directory', function () {
    $media = curationDescriptorBaselineMedia(['directory' => 'uploads/2026/01', 'path' => 'uploads/2026/01/quadrants.png']);

    $curation = curationDescriptorBaselineSave($media);

    expect($curation)->toMatchArray([
        'directory' => 'quadrants',
        'name' => 'hero.png',
        'path' => 'uploads/2026/01/quadrants/hero.png',
        'url' => '/storage/uploads/2026/01/quadrants/hero.png',
    ])
        ->and(Storage::disk('public')->exists('uploads/2026/01/quadrants/hero.png'))->toBeTrue();
});

test('media in the disk root gets a curation path with a leading slash and a doubled slash in its url', function () {
    $media = curationDescriptorBaselineMedia(['directory' => null, 'path' => 'quadrants.png']);

    $curation = curationDescriptorBaselineSave($media);

    expect($curation)->toMatchArray([
        'directory' => 'quadrants',
        'name' => 'hero.png',
        'path' => '/quadrants/hero.png',
        'url' => '/storage//quadrants/hero.png',
    ])
        ->and($curation['size'])->toBe(Storage::disk('public')->size('quadrants/hero.png'))
        ->and(Storage::disk('public')->allFiles())->toEqualCanonicalizing(['quadrants.png', 'quadrants/hero.png']);
});

test('the format sets the extension, name and type, and falls back to the media extension', function (?string $format, string $name, string $type, string $ext) {
    $media = curationDescriptorBaselineMedia();

    $curation = curationDescriptorBaselineSave($media, ['format' => $format]);

    expect($curation)->toMatchArray([
        'name' => $name,
        'path' => 'media/quadrants/' . $name,
        'type' => $type,
        'ext' => $ext,
        'url' => '/storage/media/quadrants/' . $name,
    ])
        ->and((new finfo(FILEINFO_MIME_TYPE))->buffer(Storage::disk('public')->get('media/quadrants/' . $name)))->toBe($type);
})->with([
    'jpg' => ['jpg', 'hero.jpg', 'image/jpeg', 'jpg'],
    'webp' => ['webp', 'hero.webp', 'image/webp', 'webp'],
    'png' => ['png', 'hero.png', 'image/png', 'png'],
    'none' => [null, 'hero.png', 'image/png', 'png'],
]);

test('a missing quality encodes the same bytes as quality 60', function () {
    $media = curationDescriptorBaselineMedia();

    curationDescriptorBaselineSave($media, ['key' => 'implicit', 'format' => 'jpg', 'quality' => null]);
    curationDescriptorBaselineSave($media, ['key' => 'explicit', 'format' => 'jpg', 'quality' => 60]);
    curationDescriptorBaselineSave($media, ['key' => 'high', 'format' => 'jpg', 'quality' => 95]);

    $disk = Storage::disk('public');

    expect($disk->get('media/quadrants/implicit.jpg'))->toBe($disk->get('media/quadrants/explicit.jpg'))
        ->and($disk->get('media/quadrants/high.jpg'))->not->toBe($disk->get('media/quadrants/explicit.jpg'));
});

test('a custom curation keeps the size of the crop in image pixels as integers', function (array $box, array $size) {
    $media = curationDescriptorBaselineMedia();

    $curation = curationDescriptorBaselineSave($media, $box);

    expect([$curation['width'], $curation['height']])->toBe($size)
        ->and(array_slice(getimagesizefromstring(Storage::disk('public')->get('media/quadrants/hero.png')), 0, 2))->toBe($size);
})->with([
    'whole numbers' => [['x' => 10, 'y' => 5, 'width' => 20, 'height' => 10], [20, 10]],
    'fractions floor the origin and round the far edge' => [['x' => 2.6, 'y' => 1.4, 'width' => 17.3, 'height' => 8.2], [18, 9]],
    'cropper canvas size is ignored' => [['canvasData' => ['width' => 7, 'height' => 3, 'naturalWidth' => 40, 'naturalHeight' => 20]], [20, 10]],
]);

test('a registered preset sets the descriptor size, and the payload still sets the format', function () {
    Curation::presets([
        CurationPreset::make('Banner')->width(160)->height(40)->format('png')->quality(80),
    ]);

    $media = curationDescriptorBaselineMedia();

    $curation = curationDescriptorBaselineSave($media, ['key' => 'banner', 'format' => 'webp', 'x' => 0, 'y' => 0, 'width' => 40, 'height' => 10]);

    expect($curation)->toMatchArray([
        'key' => 'banner',
        'name' => 'banner.webp',
        'path' => 'media/quadrants/banner.webp',
        'width' => 160,
        'height' => 40,
        'type' => 'image/webp',
        'ext' => 'webp',
    ])
        ->and(array_slice(getimagesizefromstring(Storage::disk('public')->get('media/quadrants/banner.webp')), 0, 2))->toBe([160, 40]);
});

test('a preset is matched by its key only, so a custom key of the same size stays custom', function () {
    Curation::presets([
        CurationPreset::make('Banner')->width(160)->height(40),
    ]);

    $media = curationDescriptorBaselineMedia();

    $curation = curationDescriptorBaselineSave($media, ['key' => 'Banner', 'x' => 0, 'y' => 0, 'width' => 40, 'height' => 10]);

    expect([$curation['key'], $curation['width'], $curation['height']])->toBe(['Banner', 40, 10]);
});

test('a key is always required, so the {width}x{height} key fallback is never reached', function () {
    $media = curationDescriptorBaselineMedia();

    $payload = curationDescriptorBaselinePayload();
    unset($payload['key']);

    curationDescriptorBaselineComponent($media)
        ->call('saveCuration', $payload)
        ->assertHasErrors(['key' => 'required'])
        ->assertNotDispatched('add-curation');

    expect(Storage::disk('public')->allFiles())->toBe(['media/quadrants.png']);
});

test('a private media curation records private visibility and a temporary url', function () {
    $media = curationDescriptorBaselineMedia(['disk' => 'local', 'visibility' => 'private']);

    $curation = curationDescriptorBaselineSave($media);

    // 2026-01-01 12:05:00 UTC: five minutes after the frozen clock.
    expect($curation)->toMatchArray([
        'disk' => 'local',
        'visibility' => 'private',
        'path' => 'media/quadrants/hero.png',
        'url' => 'http://localhost/media/quadrants/hero.png?expiration=1767269100',
    ])
        ->and(Storage::disk('local')->exists('media/quadrants/hero.png'))->toBeTrue();
});

test('saving the media edit page stores curations as a list of repeater items, each wrapping the descriptor', function () {
    $media = curationDescriptorBaselineMedia();

    $curation = curationDescriptorBaselineSave($media);

    Livewire::test(EditMedia::class, ['record' => $media->id])
        ->set('data.curations', ['5d1e7a3c-0000-4000-8000-000000000001' => ['curation' => $curation]])
        ->call('save')
        ->assertHasNoFormErrors();

    $size = Storage::disk('public')->size('media/quadrants/hero.png');

    expect(DB::table($media->getTable())->where('id', $media->id)->value('curations'))
        ->toBe('[{"curation":{"key":"hero","disk":"public","directory":"quadrants","visibility":"public","name":"hero.png","path":"media\/quadrants\/hero.png","width":20,"height":10,"size":' . $size . ',"type":"image\/png","ext":"png","url":"\/storage\/media\/quadrants\/hero.png"}}]')
        ->and($media->refresh()->getCuration('hero'))->toBe($curation)
        ->and($media->hasCuration('hero'))->toBeTrue();
});

test('a legacy row resolves through getCuration and hasCuration unchanged, stale url included', function () {
    $media = curationDescriptorBaselineLegacyMedia();

    expect($media->getCuration('thumbnail'))->toBe([
        'key' => 'thumbnail',
        'disk' => 'public',
        'directory' => 'quadrants',
        'visibility' => 'public',
        'name' => 'thumbnail.webp',
        'path' => 'media/quadrants/thumbnail.webp',
        'width' => 200,
        'height' => 100,
        'size' => 1234,
        'type' => 'image/webp',
        'ext' => 'webp',
        'url' => 'https://old.example.com/storage/media/quadrants/thumbnail.webp',
    ])
        ->and($media->hasCuration('thumbnail'))->toBeTrue()
        ->and($media->getCuration('missing'))->toBe([])
        ->and($media->hasCuration('missing'))->toBeFalse();
});

test('a legacy row keyed by repeater item uuid still resolves', function () {
    $row = '{"0b8c1f3e-1111-4222-8333-444455556666":' . substr(CURATION_DESCRIPTOR_BASELINE_LEGACY_ROW, 1, -1) . '}';

    $media = curationDescriptorBaselineLegacyMedia($row);

    expect($media->getCuration('thumbnail'))->toHaveKey('path', 'media/quadrants/thumbnail.webp')
        ->and($media->hasCuration('thumbnail'))->toBeTrue();
});

test('the first of two legacy items with the same key wins', function () {
    $second = str_replace(['thumbnail.webp', '"width":200'], ['second.webp', '"width":300'], substr(CURATION_DESCRIPTOR_BASELINE_LEGACY_ROW, 1, -1));

    $media = curationDescriptorBaselineLegacyMedia('[' . substr(CURATION_DESCRIPTOR_BASELINE_LEGACY_ROW, 1, -1) . ',' . $second . ']');

    expect($media->getCuration('thumbnail'))->toMatchArray(['name' => 'thumbnail.webp', 'width' => 200]);
});

test('the curation component renders a legacy row from its disk and path, not its stored url', function () {
    $media = curationDescriptorBaselineLegacyMedia();

    $html = Blade::render('<x-curator-curation :media="$media" curation="thumbnail" class="hero" />', ['media' => $media]);

    expect(preg_replace('/\s+/', ' ', trim($html)))
        ->toBe('<img src="/storage/media/quadrants/thumbnail.webp" alt="Quadrants" width="200" height="100" class="hero" />');
});

test('the curation component renders a private legacy row with a temporary url', function () {
    $row = str_replace(['"disk":"public"', '"visibility":"public"'], ['"disk":"local"', '"visibility":"private"'], CURATION_DESCRIPTOR_BASELINE_LEGACY_ROW);

    $media = curationDescriptorBaselineLegacyMedia($row, ['disk' => 'local', 'visibility' => 'private']);

    expect(Blade::render('<x-curator-curation :media="$media" curation="thumbnail" />', ['media' => $media]))
        ->toContain('src="http://localhost/media/quadrants/thumbnail.webp?expiration=1767269100"');
});

test('the curation component falls back to the default visibility for a row without one', function () {
    $row = str_replace(',"visibility":"public"', '', CURATION_DESCRIPTOR_BASELINE_LEGACY_ROW);

    $media = curationDescriptorBaselineLegacyMedia($row);

    expect($media->getCuration('thumbnail'))->not->toHaveKey('visibility')
        ->and(Blade::render('<x-curator-curation :media="$media" curation="thumbnail" />', ['media' => $media]))
        ->toContain('src="/storage/media/quadrants/thumbnail.webp"');
});

test('the curation editor hydrates a legacy row with a freshly resolved url, and saving writes it back', function () {
    $media = curationDescriptorBaselineLegacyMedia();

    $component = Livewire::test(EditMedia::class, ['record' => $media->id]);

    $items = $component->get('data.curations');

    expect($items)->toHaveCount(1)
        ->and(array_values($items)[0])->toBe(['curation' => [
            'key' => 'thumbnail',
            'disk' => 'public',
            'directory' => 'quadrants',
            'visibility' => 'public',
            'name' => 'thumbnail.webp',
            'path' => 'media/quadrants/thumbnail.webp',
            'width' => 200,
            'height' => 100,
            'size' => 1234,
            'type' => 'image/webp',
            'ext' => 'webp',
            'url' => '/storage/media/quadrants/thumbnail.webp',
        ]]);

    $component->call('save')->assertHasNoFormErrors();

    expect(DB::table($media->getTable())->where('id', $media->id)->value('curations'))
        ->toBe(str_replace('https:\/\/old.example.com\/storage', '\/storage', CURATION_DESCRIPTOR_BASELINE_LEGACY_ROW));
});

test('the curation editor hydrates a private legacy row with a temporary url', function () {
    $row = str_replace(['"disk":"public"', '"visibility":"public"'], ['"disk":"local"', '"visibility":"private"'], CURATION_DESCRIPTOR_BASELINE_LEGACY_ROW);

    $media = curationDescriptorBaselineLegacyMedia($row, ['disk' => 'local', 'visibility' => 'private']);

    $items = Livewire::test(EditMedia::class, ['record' => $media->id])->get('data.curations');

    expect(array_values($items)[0]['curation']['url'])->toBe('http://localhost/media/quadrants/thumbnail.webp?expiration=1767269100');
});
