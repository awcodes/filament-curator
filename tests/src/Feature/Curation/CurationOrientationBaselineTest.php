<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorCuration;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

// Characterization tests for how 5.x (intervention/image 3 on GD) orients
// images: EXIF-rotated JPEG uploads, Glide output for them, curations cut from
// them, and cropper flips combined with quarter turns.
//
// exif/orientation-6.jpg and exif/orientation-3.jpg hold the same stored
// pixels, a 48x32 JPEG with four flat 24x16 quadrants (red top left, green top
// right, blue bottom left, yellow bottom right), and differ only in the EXIF
// Orientation tag: 6 (turn 90° clockwise to display) and 3 (turn 180°).
//
// Upright, orientation 6 is 32x48 with blue top left, red top right, yellow
// bottom left and green bottom right; orientation 3 is 48x32 with yellow top
// left, blue top right, green bottom left and red bottom right.
//
// JPEG pixels are compared within a tolerance; PNG pixels exactly.

const CURATION_ORIENTATION_BASELINE_RED = [255, 0, 0];
const CURATION_ORIENTATION_BASELINE_GREEN = [0, 255, 0];
const CURATION_ORIENTATION_BASELINE_BLUE = [0, 0, 255];
const CURATION_ORIENTATION_BASELINE_YELLOW = [255, 255, 0];
const CURATION_ORIENTATION_BASELINE_JPEG_DELTA = 24;

beforeEach(function () {
    $this->storagePath = sys_get_temp_dir() . '/curator-curation-orientation-baseline-' . Str::random(12);
    File::ensureDirectoryExists($this->storagePath . '/app');
    $this->app->useStoragePath($this->storagePath);

    Storage::fake('public');
    Storage::fake('local');

    config()->set('curator.glide_token', 'baseline-token');
    config()->set('curator.default_disk', 'public');

    $this->travelTo(Carbon::parse('2026-01-01 12:00:00', 'UTC'));
});

afterEach(function () {
    File::deleteDirectory($this->storagePath);
});

function curationOrientationBaselineFixture(string $name): string
{
    return File::get(__DIR__ . '/../../Fixtures/Files/' . $name);
}

/**
 * Uploads a fixture through the media create page, as a user would.
 */
function curationOrientationBaselineUpload(string $fixture): Media
{
    $content = curationOrientationBaselineFixture($fixture);

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent(basename($fixture), $content)->mimeType('image/jpeg'))
        ->call('create')
        ->assertHasNoFormErrors();

    return Media::query()->sole();
}

/**
 * Stores a fixture and creates its media row directly, for tests that start from existing media.
 */
function curationOrientationBaselineMedia(string $fixture, array $overrides = []): Media
{
    $ext = pathinfo($fixture, PATHINFO_EXTENSION);
    $name = pathinfo($fixture, PATHINFO_FILENAME);

    Storage::disk('public')->put('media/' . $name . '.' . $ext, curationOrientationBaselineFixture($fixture));

    return makeMedia(array_merge([
        'directory' => 'media',
        'name' => $name,
        'path' => 'media/' . $name . '.' . $ext,
        'ext' => $ext,
        'type' => $ext === 'png' ? 'image/png' : 'image/jpeg',
    ], $overrides));
}

/**
 * Saves a png curation with the given crop and cropper transform, and returns the decoded output.
 */
function curationOrientationBaselineCurate(Media $media, array $crop): GdImage
{
    Livewire::test(CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.curation',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ])
        ->call('saveCuration', array_replace([
            'key' => 'crop',
            'format' => 'png',
            'quality' => 60,
            'rotate' => 0,
            'scaleX' => 1,
            'scaleY' => 1,
            'canvasData' => ['width' => 480, 'height' => 320, 'naturalWidth' => 48, 'naturalHeight' => 32],
        ], $crop))
        ->assertHasNoErrors()
        ->assertDispatched('add-curation');

    $image = imagecreatefromstring(Storage::disk('public')->get('media/' . $media->name . '/crop.png'));

    expect($image)->toBeInstanceOf(GdImage::class);

    return $image;
}

/**
 * @return array{0: int, 1: int, 2: int}
 */
function curationOrientationBaselinePixel(GdImage $image, int $x, int $y): array
{
    $color = imagecolorat($image, $x, $y);

    return [($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF];
}

/**
 * Samples the centre of each quadrant of the image, in the order top left, top right, bottom left, bottom right.
 *
 * @return array<int, array{0: int, 1: int, 2: int}>
 */
function curationOrientationBaselineQuadrants(GdImage $image): array
{
    $width = imagesx($image);
    $height = imagesy($image);

    return array_map(
        fn (array $point): array => curationOrientationBaselinePixel($image, (int) ($width * $point[0]), (int) ($height * $point[1])),
        [[0.25, 0.25], [0.75, 0.25], [0.25, 0.75], [0.75, 0.75]],
    );
}

function curationOrientationBaselineAssertColors(array $actual, array $expected, int $delta): void
{
    foreach ($expected as $i => $color) {
        foreach ($color as $channel => $value) {
            expect(abs($actual[$i][$channel] - $value))->toBeLessThanOrEqual($delta, "sample {$i} channel {$channel}: " . json_encode($actual[$i]) . ' vs ' . json_encode($color));
        }
    }
}

test('the exif fixtures carry their orientation and are stored sideways', function (string $fixture, int $orientation) {
    $content = curationOrientationBaselineFixture($fixture);

    expect(strlen($content))->toBeLessThan(2048)
        ->and(exif_read_data('data://image/jpeg;base64,' . base64_encode($content))['Orientation'])->toBe($orientation)
        ->and(array_slice(getimagesizefromstring($content), 0, 2))->toBe([48, 32]);
})->with([
    'orientation 6' => ['exif/orientation-6.jpg', 6],
    'orientation 3' => ['exif/orientation-3.jpg', 3],
]);

test('an uploaded exif rotated jpeg records upright dimensions and an exif orientation reset to 1', function (string $fixture, array $size) {
    $media = curationOrientationBaselineUpload($fixture);

    expect([$media->width, $media->height])->toBe($size)
        ->and(array_keys($media->exif))->toBe(['FILE', 'COMPUTED', 'IFD0', 'COMMENT'])
        ->and($media->exif['IFD0'])->toBe(['Orientation' => 1])
        ->and($media->exif['COMPUTED'])->toMatchArray(['Width' => 48, 'Height' => 32])
        ->and($media->exif['FILE'])->toMatchArray(['FileSize' => 805, 'MimeType' => 'image/jpeg']);
})->with([
    'orientation 6' => ['exif/orientation-6.jpg', [32, 48]],
    'orientation 3' => ['exif/orientation-3.jpg', [48, 32]],
]);

test('an uploaded exif rotated jpeg is stored byte for byte, still sideways and still tagged', function (string $fixture, int $orientation) {
    $media = curationOrientationBaselineUpload($fixture);

    $stored = Storage::disk('public')->get($media->path);

    expect($stored)->toBe(curationOrientationBaselineFixture($fixture))
        ->and(exif_read_data('data://image/jpeg;base64,' . base64_encode($stored))['Orientation'])->toBe($orientation);
})->with([
    'orientation 6' => ['exif/orientation-6.jpg', 6],
    'orientation 3' => ['exif/orientation-3.jpg', 3],
]);

test('glide serves an exif rotated jpeg upright', function (string $fixture, array $params, array $size, array $colors) {
    $media = curationOrientationBaselineMedia($fixture);

    $response = $this->get(Glide::getUrl($media->path, $params));

    $response->assertOk()->assertHeader('Content-Type', 'image/jpeg');

    $image = imagecreatefromstring($response->streamedContent());

    expect([imagesx($image), imagesy($image)])->toBe($size);

    curationOrientationBaselineAssertColors(curationOrientationBaselineQuadrants($image), $colors, CURATION_ORIENTATION_BASELINE_JPEG_DELTA);
})->with([
    'orientation 6 untransformed' => ['exif/orientation-6.jpg', [], [32, 48], [CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_RED, CURATION_ORIENTATION_BASELINE_YELLOW, CURATION_ORIENTATION_BASELINE_GREEN]],
    'orientation 6 resized by width' => ['exif/orientation-6.jpg', ['w' => 16], [16, 24], [CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_RED, CURATION_ORIENTATION_BASELINE_YELLOW, CURATION_ORIENTATION_BASELINE_GREEN]],
    'orientation 6 fit crop' => ['exif/orientation-6.jpg', ['w' => 32, 'h' => 32, 'fit' => 'crop'], [32, 32], [CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_RED, CURATION_ORIENTATION_BASELINE_YELLOW, CURATION_ORIENTATION_BASELINE_GREEN]],
    'orientation 3 untransformed' => ['exif/orientation-3.jpg', [], [48, 32], [CURATION_ORIENTATION_BASELINE_YELLOW, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_RED]],
    'orientation 3 resized by width' => ['exif/orientation-3.jpg', ['w' => 24], [24, 16], [CURATION_ORIENTATION_BASELINE_YELLOW, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_RED]],
]);

test('glide output for an exif rotated jpeg carries no orientation tag', function () {
    $media = curationOrientationBaselineMedia('exif/orientation-6.jpg');

    $content = $this->get(Glide::getUrl($media->path, ['w' => 16]))->streamedContent();

    expect(@exif_read_data('data://image/jpeg;base64,' . base64_encode($content)) ?: [])->not->toHaveKey('Orientation');
});

test('a curation of an exif rotated jpeg measures the crop on the upright image', function (string $fixture, array $crop, array $size, array $samples) {
    $media = curationOrientationBaselineMedia($fixture);

    $image = curationOrientationBaselineCurate($media, $crop);

    expect([imagesx($image), imagesy($image)])->toBe($size);

    curationOrientationBaselineAssertColors(
        array_map(fn (array $sample): array => curationOrientationBaselinePixel($image, ...$sample[0]), $samples),
        array_column($samples, 1),
        CURATION_ORIENTATION_BASELINE_JPEG_DELTA,
    );
})->with([
    // Upright 32x48; the box x 4..24, y 6..36 spans all four quadrants, which meet at (16, 24).
    'orientation 6' => ['exif/orientation-6.jpg', ['x' => 4, 'y' => 6, 'width' => 20, 'height' => 30], [20, 30], [
        [[4, 6], CURATION_ORIENTATION_BASELINE_BLUE],
        [[16, 6], CURATION_ORIENTATION_BASELINE_RED],
        [[4, 24], CURATION_ORIENTATION_BASELINE_YELLOW],
        [[16, 24], CURATION_ORIENTATION_BASELINE_GREEN],
    ]],
    // Upright 48x32; the box x 14..38, y 4..24 spans all four quadrants, which meet at (24, 16).
    'orientation 3' => ['exif/orientation-3.jpg', ['x' => 14, 'y' => 4, 'width' => 24, 'height' => 20], [24, 20], [
        [[4, 4], CURATION_ORIENTATION_BASELINE_YELLOW],
        [[18, 4], CURATION_ORIENTATION_BASELINE_BLUE],
        [[4, 16], CURATION_ORIENTATION_BASELINE_GREEN],
        [[18, 16], CURATION_ORIENTATION_BASELINE_RED],
    ]],
    // Turned a further 90° clockwise in the cropper, orientation 6 is 48x32 and laid out like orientation 3.
    'orientation 6 turned clockwise in the cropper' => ['exif/orientation-6.jpg', ['x' => 14, 'y' => 4, 'width' => 24, 'height' => 20, 'rotate' => 90], [24, 20], [
        [[4, 4], CURATION_ORIENTATION_BASELINE_YELLOW],
        [[18, 4], CURATION_ORIENTATION_BASELINE_BLUE],
        [[4, 16], CURATION_ORIENTATION_BASELINE_GREEN],
        [[18, 16], CURATION_ORIENTATION_BASELINE_RED],
    ]],
    // A crop reaching past the upright 48x32 image is trimmed to its bottom edge.
    'orientation 3 trimmed to the upright height' => ['exif/orientation-3.jpg', ['x' => 4, 'y' => 6, 'width' => 20, 'height' => 30], [20, 26], [
        [[2, 2], CURATION_ORIENTATION_BASELINE_YELLOW],
        [[2, 20], CURATION_ORIENTATION_BASELINE_GREEN],
    ]],
]);

test('a cropper flip combined with a quarter turn and an off-centre crop lands each quadrant where the cropper shows it', function (array $transform, array $colors) {
    // quadrants.png: 40x20, red top left, green top right, blue bottom left, yellow bottom right.
    $media = curationOrientationBaselineMedia('glide/quadrants.png', ['width' => 40, 'height' => 20]);

    // After a quarter turn the image is 20x40, quadrants 10x20 meeting at (10, 20).
    // The box x 2..16, y 4..34 is off centre and spans all four.
    $image = curationOrientationBaselineCurate($media, array_merge(['x' => 2, 'y' => 4, 'width' => 14, 'height' => 30], $transform));

    expect([imagesx($image), imagesy($image)])->toBe([14, 30]);

    curationOrientationBaselineAssertColors(
        [
            curationOrientationBaselinePixel($image, 0, 0),
            curationOrientationBaselinePixel($image, 13, 0),
            curationOrientationBaselinePixel($image, 0, 29),
            curationOrientationBaselinePixel($image, 13, 29),
            curationOrientationBaselinePixel($image, 7, 15),
            curationOrientationBaselinePixel($image, 8, 15),
            curationOrientationBaselinePixel($image, 7, 16),
            curationOrientationBaselinePixel($image, 8, 16),
        ],
        $colors,
        0,
    );
})->with([
    // Mirrored left to right, then turned clockwise: flipped across the anti-diagonal.
    'scaleX -1, rotate 90' => [['scaleX' => -1, 'rotate' => 90], [
        CURATION_ORIENTATION_BASELINE_YELLOW, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_RED,
        CURATION_ORIENTATION_BASELINE_YELLOW, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_RED,
    ]],
    // Mirrored top to bottom, then turned anticlockwise: also flipped across the anti-diagonal.
    'scaleY -1, rotate -90' => [['scaleY' => -1, 'rotate' => -90], [
        CURATION_ORIENTATION_BASELINE_YELLOW, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_RED,
        CURATION_ORIENTATION_BASELINE_YELLOW, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_RED,
    ]],
    // Mirrored left to right, then turned anticlockwise: flipped across the main diagonal.
    'scaleX -1, rotate -90' => [['scaleX' => -1, 'rotate' => -90], [
        CURATION_ORIENTATION_BASELINE_RED, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_YELLOW,
        CURATION_ORIENTATION_BASELINE_RED, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_YELLOW,
    ]],
    // Mirrored top to bottom, then turned clockwise: also flipped across the main diagonal.
    'scaleY -1, rotate 90' => [['scaleY' => -1, 'rotate' => 90], [
        CURATION_ORIENTATION_BASELINE_RED, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_YELLOW,
        CURATION_ORIENTATION_BASELINE_RED, CURATION_ORIENTATION_BASELINE_BLUE, CURATION_ORIENTATION_BASELINE_GREEN, CURATION_ORIENTATION_BASELINE_YELLOW,
    ]],
]);

test('a cropper flip combined with a quarter turn maps every pixel of an off-centre crop', function (array $transform, Closure $source) {
    // gradient.png: 32x32, red = 8x, green = 8y, blue 128.
    $media = curationOrientationBaselineMedia('glide/gradient.png', ['width' => 32, 'height' => 32]);

    $image = curationOrientationBaselineCurate($media, array_merge(['x' => 3, 'y' => 5, 'width' => 20, 'height' => 12], $transform));

    expect([imagesx($image), imagesy($image)])->toBe([20, 12]);

    $mismatches = [];

    for ($i = 0; $i < 20; $i++) {
        for ($j = 0; $j < 12; $j++) {
            [$x, $y] = $source(3 + $i, 5 + $j);
            $expected = [$x * 8, $y * 8, 128];
            $actual = curationOrientationBaselinePixel($image, $i, $j);

            if ($actual !== $expected) {
                $mismatches[] = "({$i}, {$j}): " . json_encode($actual) . ' vs ' . json_encode($expected);
            }
        }
    }

    expect($mismatches)->toBe([]);
})->with([
    // Each closure maps a pixel of the transformed 32x32 image back to the source pixel it shows.
    'scaleX -1, rotate 90' => [['scaleX' => -1, 'rotate' => 90], fn (int $x, int $y): array => [31 - $y, 31 - $x]],
    'scaleY -1, rotate -90' => [['scaleY' => -1, 'rotate' => -90], fn (int $x, int $y): array => [31 - $y, 31 - $x]],
    'scaleX -1, rotate -90' => [['scaleX' => -1, 'rotate' => -90], fn (int $x, int $y): array => [$y, $x]],
    'scaleY -1, rotate 90' => [['scaleY' => -1, 'rotate' => 90], fn (int $x, int $y): array => [$y, $x]],
    'scaleX -1, scaleY -1, rotate 90' => [['scaleX' => -1, 'scaleY' => -1, 'rotate' => 90], fn (int $x, int $y): array => [31 - $y, $x]],
    'rotate 180' => [['rotate' => 180], fn (int $x, int $y): array => [31 - $x, 31 - $y]],
]);
