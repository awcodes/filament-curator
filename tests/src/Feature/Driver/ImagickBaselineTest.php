<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorCuration;
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Facades\Curation;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GlideBuilder;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Livewire\Livewire;

// Characterization tests for 5.x on the Imagick driver, the other driver
// Glide and intervention/image 3 support. They mirror the GD baselines in
// Glide/GlideOutputBaselineTest.php and Curation/CurationOrientationBaselineTest.php
// so a later change of driver contract can be checked on both.
//
// An app picks Imagick through Glide::serverConfig(), as docs/storage/glide.md
// shows. Uploads, curations and Glide all read through that server's image
// manager, so the one setting reaches each of them. A custom server reads
// every disk through its single source, so only public media is covered.
//
// Fixtures and colours are those of the GD baselines. Outputs are decoded with
// GD, so only the driver that wrote them differs.

const IMAGICK_BASELINE_RED = [255, 0, 0];
const IMAGICK_BASELINE_GREEN = [0, 255, 0];
const IMAGICK_BASELINE_BLUE = [0, 0, 255];
const IMAGICK_BASELINE_YELLOW = [255, 255, 0];
const IMAGICK_BASELINE_JPEG_DELTA = 24;

beforeEach(function () {
    if (! extension_loaded('imagick')) {
        $this->markTestSkipped('The imagick extension is not loaded, so the Imagick driver cannot be characterized.');
    }

    $this->storagePath = sys_get_temp_dir() . '/curator-imagick-baseline-' . Str::random(12);
    File::ensureDirectoryExists($this->storagePath . '/app');
    $this->app->useStoragePath($this->storagePath);

    Storage::fake('public');

    config()->set('curator.glide_token', 'baseline-token');
    config()->set('curator.default_disk', 'public');

    $this->travelTo(Carbon::parse('2026-01-01 12:00:00', 'UTC'));

    Glide::serverConfig([
        'driver' => 'imagick',
        'source' => Storage::disk('public')->getDriver(),
        'cache' => storage_path('app'),
        'cache_path_prefix' => '.cache',
        'max_image_size' => 2000 * 2000,
    ]);
});

afterEach(function () {
    if (isset($this->storagePath)) {
        File::deleteDirectory($this->storagePath);
    }
});

function imagickBaselineFixture(string $name): string
{
    return File::get(__DIR__ . '/../../Fixtures/Files/' . $name);
}

function imagickBaselineMedia(string $fixture, array $overrides = []): Media
{
    $ext = pathinfo($fixture, PATHINFO_EXTENSION);
    $name = pathinfo($fixture, PATHINFO_FILENAME);

    Storage::disk('public')->put('media/' . $name . '.' . $ext, imagickBaselineFixture($fixture));

    return makeMedia(array_merge([
        'directory' => 'media',
        'name' => $name,
        'path' => 'media/' . $name . '.' . $ext,
        'ext' => $ext,
        'type' => $ext === 'png' ? 'image/png' : 'image/jpeg',
    ], $overrides));
}

function imagickBaselineQuadrantsMedia(): Media
{
    return imagickBaselineMedia('glide/quadrants.png', ['width' => 40, 'height' => 20]);
}

function imagickBaselineUpload(string $fixture): Media
{
    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent(basename($fixture), imagickBaselineFixture($fixture))->mimeType('image/jpeg'))
        ->call('create')
        ->assertHasNoFormErrors();

    return Media::query()->sole();
}

function imagickBaselineDecode(string $content): GdImage
{
    $image = imagecreatefromstring($content);

    expect($image)->toBeInstanceOf(GdImage::class);

    return $image;
}

function imagickBaselineServe(TestResponse $response, string $contentType): GdImage
{
    $response->assertOk()->assertHeader('Content-Type', $contentType);

    return imagickBaselineDecode($response->streamedContent());
}

/**
 * @return array{0: int, 1: int}
 */
function imagickBaselineSize(GdImage $image): array
{
    return [imagesx($image), imagesy($image)];
}

/**
 * @return array{0: int, 1: int, 2: int}
 */
function imagickBaselinePixel(GdImage $image, int $x, int $y): array
{
    $color = imagecolorat($image, $x, $y);

    return [($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF];
}

/**
 * Samples the centre of each quadrant, in the order top left, top right, bottom left, bottom right.
 *
 * @return array<int, array{0: int, 1: int, 2: int}>
 */
function imagickBaselineQuadrants(GdImage $image): array
{
    [$width, $height] = imagickBaselineSize($image);

    return array_map(
        fn (array $point): array => imagickBaselinePixel($image, (int) ($width * $point[0]), (int) ($height * $point[1])),
        [[0.25, 0.25], [0.75, 0.25], [0.25, 0.75], [0.75, 0.75]],
    );
}

function imagickBaselineAssertColors(array $actual, array $expected, int $delta): void
{
    foreach ($expected as $i => $color) {
        foreach ($color as $channel => $value) {
            expect(abs($actual[$i][$channel] - $value))->toBeLessThanOrEqual($delta, "sample {$i} channel {$channel}: " . json_encode($actual[$i]) . ' vs ' . json_encode($color));
        }
    }
}

/**
 * Saves a curation with the given crop and cropper transform, and returns the decoded output.
 */
function imagickBaselineCurate(Media $media, array $crop): GdImage
{
    $data = array_replace([
        'key' => 'crop',
        'format' => 'png',
        'quality' => 60,
        'rotate' => 0,
        'scaleX' => 1,
        'scaleY' => 1,
        'canvasData' => ['width' => 480, 'height' => 320, 'naturalWidth' => 48, 'naturalHeight' => 32],
    ], $crop);

    Livewire::test(CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.curation',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ])
        ->call('saveCuration', $data)
        ->assertHasNoErrors()
        ->assertDispatched('add-curation');

    return imagickBaselineDecode(Storage::disk('public')->get('media/' . $media->name . '/' . $data['key'] . '.' . $data['format']));
}

test('the configured server reads images with the imagick driver', function () {
    expect(Glide::getServer()->getApi()->getImageManager()->driver())->toBeInstanceOf(ImagickDriver::class);
});

test('an untransformed png is served as the original png', function () {
    imagickBaselineQuadrantsMedia();

    $image = imagickBaselineServe($this->get(Glide::getUrl('media/quadrants.png')), 'image/png');

    expect(imagickBaselineSize($image))->toBe([40, 20]);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), [IMAGICK_BASELINE_RED, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_YELLOW], 0);
});

test('resizing by width, height or both keeps the aspect ratio', function (array $params, array $size) {
    imagickBaselineQuadrantsMedia();

    $image = imagickBaselineServe($this->get(Glide::getUrl('media/quadrants.png', $params)), 'image/png');

    expect(imagickBaselineSize($image))->toBe($size);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), [IMAGICK_BASELINE_RED, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_YELLOW], 0);
})->with([
    'width' => [['w' => 20], [20, 10]],
    'height' => [['h' => 10], [20, 10]],
    'width and height fit inside the box' => [['w' => 20, 'h' => 20], [20, 10]],
    'upscaled width' => [['w' => 100], [100, 50]],
    'device pixel ratio' => [['w' => 20, 'dpr' => 2], [40, 20]],
]);

test('fit crop fills the box from the centre', function () {
    imagickBaselineQuadrantsMedia();

    $image = imagickBaselineServe($this->get(Glide::getUrl('media/quadrants.png', ['w' => 20, 'h' => 20, 'fit' => 'crop'])), 'image/png');

    expect(imagickBaselineSize($image))->toBe([20, 20]);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), [IMAGICK_BASELINE_RED, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_YELLOW], 0);
});

test('fit crop with a focal position keeps that side of the image', function () {
    imagickBaselineQuadrantsMedia();

    $image = imagickBaselineServe($this->get(Glide::getUrl('media/quadrants.png', ['w' => 10, 'h' => 20, 'fit' => 'crop-left'])), 'image/png');

    expect(imagickBaselineSize($image))->toBe([10, 20]);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), [IMAGICK_BASELINE_RED, IMAGICK_BASELINE_RED, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_BLUE], 0);
});

test('a manual crop from the glide builder cuts out the given rectangle', function () {
    imagickBaselineQuadrantsMedia();

    $image = imagickBaselineServe($this->get(GlideBuilder::make()->crop(20, 10, 20, 10)->toUrl('media/quadrants.png')), 'image/png');

    expect(imagickBaselineSize($image))->toBe([20, 10]);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), array_fill(0, 4, IMAGICK_BASELINE_YELLOW), 0);
});

test('format conversion changes the content type and keeps the pixels', function (string $format, string $contentType, int $delta) {
    imagickBaselineQuadrantsMedia();

    $image = imagickBaselineServe($this->get(Glide::getUrl('media/quadrants.png', ['fm' => $format])), $contentType);

    expect(imagickBaselineSize($image))->toBe([40, 20]);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), [IMAGICK_BASELINE_RED, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_YELLOW], $delta);
})->with([
    'webp' => ['webp', 'image/webp', 12],
    'jpg' => ['jpg', 'image/jpeg', 16],
]);

test('the size urls serve webp at their fixed sizes, upscaling small originals', function (string $attribute, array $size) {
    $media = imagickBaselineQuadrantsMedia();

    $image = imagickBaselineServe($this->get($media->{$attribute}), 'image/webp');

    expect(imagickBaselineSize($image))->toBe($size);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), [IMAGICK_BASELINE_RED, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_YELLOW], 12);
})->with([
    'thumbnail' => ['thumbnail_url', [200, 200]],
    'medium' => ['medium_url', [640, 640]],
    'large' => ['large_url', [1024, 512]],
]);

// Differs from GD, which resets the recorded orientation to 1: Imagick turns the pixels upright but records the
// file's own tag, so the upright dimensions sit beside an orientation that would turn them again.
test('an uploaded exif rotated jpeg records upright dimensions and keeps its exif orientation', function (string $fixture, array $size, int $orientation) {
    $media = imagickBaselineUpload($fixture);

    expect([$media->width, $media->height])->toBe($size)
        ->and(array_keys($media->exif))->toBe(['FILE', 'COMPUTED', 'IFD0', 'COMMENT'])
        ->and($media->exif['IFD0'])->toBe(['Orientation' => $orientation])
        ->and($media->exif['COMPUTED'])->toMatchArray(['Width' => 48, 'Height' => 32])
        ->and($media->exif['FILE'])->toMatchArray(['FileSize' => 805, 'MimeType' => 'image/jpeg']);
})->with([
    'orientation 6' => ['exif/orientation-6.jpg', [32, 48], 6],
    'orientation 3' => ['exif/orientation-3.jpg', [48, 32], 3],
]);

test('glide serves an exif rotated jpeg upright', function (string $fixture, array $params, array $size, array $colors) {
    $media = imagickBaselineMedia($fixture);

    $image = imagickBaselineServe($this->get(Glide::getUrl($media->path, $params)), 'image/jpeg');

    expect(imagickBaselineSize($image))->toBe($size);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), $colors, IMAGICK_BASELINE_JPEG_DELTA);
})->with([
    'orientation 6 untransformed' => ['exif/orientation-6.jpg', [], [32, 48], [IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_RED, IMAGICK_BASELINE_YELLOW, IMAGICK_BASELINE_GREEN]],
    'orientation 6 resized by width' => ['exif/orientation-6.jpg', ['w' => 16], [16, 24], [IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_RED, IMAGICK_BASELINE_YELLOW, IMAGICK_BASELINE_GREEN]],
    'orientation 6 fit crop' => ['exif/orientation-6.jpg', ['w' => 32, 'h' => 32, 'fit' => 'crop'], [32, 32], [IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_RED, IMAGICK_BASELINE_YELLOW, IMAGICK_BASELINE_GREEN]],
    'orientation 3 untransformed' => ['exif/orientation-3.jpg', [], [48, 32], [IMAGICK_BASELINE_YELLOW, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_RED]],
    'orientation 3 resized by width' => ['exif/orientation-3.jpg', ['w' => 24], [24, 16], [IMAGICK_BASELINE_YELLOW, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_RED]],
]);

// Differs from GD, which writes no EXIF at all: Imagick keeps an EXIF block with the orientation reset to 1.
test('glide output for an exif rotated jpeg is tagged with the upright orientation', function () {
    $media = imagickBaselineMedia('exif/orientation-6.jpg');

    $content = $this->get(Glide::getUrl($media->path, ['w' => 16]))->streamedContent();

    expect(exif_read_data('data://image/jpeg;base64,' . base64_encode($content))['Orientation'])->toBe(1);
});

test('a curation of an exif rotated jpeg measures the crop on the upright image', function (string $fixture, array $crop, array $size, array $samples) {
    $image = imagickBaselineCurate(imagickBaselineMedia($fixture), $crop);

    expect(imagickBaselineSize($image))->toBe($size);

    imagickBaselineAssertColors(
        array_map(fn (array $sample): array => imagickBaselinePixel($image, ...$sample[0]), $samples),
        array_column($samples, 1),
        IMAGICK_BASELINE_JPEG_DELTA,
    );
})->with([
    'orientation 6' => ['exif/orientation-6.jpg', ['x' => 4, 'y' => 6, 'width' => 20, 'height' => 30], [20, 30], [
        [[4, 6], IMAGICK_BASELINE_BLUE],
        [[16, 6], IMAGICK_BASELINE_RED],
        [[4, 24], IMAGICK_BASELINE_YELLOW],
        [[16, 24], IMAGICK_BASELINE_GREEN],
    ]],
    'orientation 3' => ['exif/orientation-3.jpg', ['x' => 14, 'y' => 4, 'width' => 24, 'height' => 20], [24, 20], [
        [[4, 4], IMAGICK_BASELINE_YELLOW],
        [[18, 4], IMAGICK_BASELINE_BLUE],
        [[4, 16], IMAGICK_BASELINE_GREEN],
        [[18, 16], IMAGICK_BASELINE_RED],
    ]],
    'orientation 6 turned clockwise in the cropper' => ['exif/orientation-6.jpg', ['x' => 14, 'y' => 4, 'width' => 24, 'height' => 20, 'rotate' => 90], [24, 20], [
        [[4, 4], IMAGICK_BASELINE_YELLOW],
        [[18, 4], IMAGICK_BASELINE_BLUE],
        [[4, 16], IMAGICK_BASELINE_GREEN],
        [[18, 16], IMAGICK_BASELINE_RED],
    ]],
    'orientation 3 trimmed to the upright height' => ['exif/orientation-3.jpg', ['x' => 4, 'y' => 6, 'width' => 20, 'height' => 30], [20, 26], [
        [[2, 2], IMAGICK_BASELINE_YELLOW],
        [[2, 20], IMAGICK_BASELINE_GREEN],
    ]],
]);

test('a cropper flip combined with a quarter turn and an off-centre crop lands each quadrant where the cropper shows it', function (array $transform, array $colors) {
    $image = imagickBaselineCurate(imagickBaselineQuadrantsMedia(), array_merge(['x' => 2, 'y' => 4, 'width' => 14, 'height' => 30], $transform));

    expect(imagickBaselineSize($image))->toBe([14, 30]);

    imagickBaselineAssertColors(
        array_map(fn (array $point): array => imagickBaselinePixel($image, ...$point), [[0, 0], [13, 0], [0, 29], [13, 29], [7, 15], [8, 15], [7, 16], [8, 16]]),
        $colors,
        0,
    );
})->with([
    'scaleX -1, rotate 90' => [['scaleX' => -1, 'rotate' => 90], [
        IMAGICK_BASELINE_YELLOW, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_RED,
        IMAGICK_BASELINE_YELLOW, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_RED,
    ]],
    'scaleY -1, rotate -90' => [['scaleY' => -1, 'rotate' => -90], [
        IMAGICK_BASELINE_YELLOW, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_RED,
        IMAGICK_BASELINE_YELLOW, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_RED,
    ]],
    'scaleX -1, rotate -90' => [['scaleX' => -1, 'rotate' => -90], [
        IMAGICK_BASELINE_RED, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_YELLOW,
        IMAGICK_BASELINE_RED, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_YELLOW,
    ]],
    'scaleY -1, rotate 90' => [['scaleY' => -1, 'rotate' => 90], [
        IMAGICK_BASELINE_RED, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_YELLOW,
        IMAGICK_BASELINE_RED, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_YELLOW,
    ]],
]);

test('a cropper flip combined with a quarter turn maps every pixel of an off-centre crop', function (array $transform, Closure $source) {
    $image = imagickBaselineCurate(imagickBaselineMedia('glide/gradient.png', ['width' => 32, 'height' => 32]), array_merge(['x' => 3, 'y' => 5, 'width' => 20, 'height' => 12], $transform));

    expect(imagickBaselineSize($image))->toBe([20, 12]);

    $mismatches = [];

    for ($i = 0; $i < 20; $i++) {
        for ($j = 0; $j < 12; $j++) {
            [$x, $y] = $source(3 + $i, 5 + $j);
            $expected = [$x * 8, $y * 8, 128];
            $actual = imagickBaselinePixel($image, $i, $j);

            if ($actual !== $expected) {
                $mismatches[] = "({$i}, {$j}): " . json_encode($actual) . ' vs ' . json_encode($expected);
            }
        }
    }

    expect($mismatches)->toBe([]);
})->with([
    'scaleX -1, rotate 90' => [['scaleX' => -1, 'rotate' => 90], fn (int $x, int $y): array => [31 - $y, 31 - $x]],
    'scaleY -1, rotate -90' => [['scaleY' => -1, 'rotate' => -90], fn (int $x, int $y): array => [31 - $y, 31 - $x]],
    'scaleX -1, rotate -90' => [['scaleX' => -1, 'rotate' => -90], fn (int $x, int $y): array => [$y, $x]],
    'scaleY -1, rotate 90' => [['scaleY' => -1, 'rotate' => 90], fn (int $x, int $y): array => [$y, $x]],
    'scaleX -1, scaleY -1, rotate 90' => [['scaleX' => -1, 'scaleY' => -1, 'rotate' => 90], fn (int $x, int $y): array => [31 - $y, $x]],
    'rotate 180' => [['rotate' => 180], fn (int $x, int $y): array => [31 - $x, 31 - $y]],
]);

test('a custom curation keeps the crop size in natural pixels, scaled down to the maximum dimension', function (?int $limit, array $size) {
    if ($limit !== null) {
        config()->set('curator.curation_max_dimension', $limit);
    }

    $image = imagickBaselineCurate(imagickBaselineQuadrantsMedia(), ['x' => 0, 'y' => 0, 'width' => 40, 'height' => 20]);

    expect(imagickBaselineSize($image))->toBe($size);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), [IMAGICK_BASELINE_RED, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_YELLOW], 0);
})->with([
    'natural size' => [null, [40, 20]],
    'scaled to a maximum of 20' => [20, [20, 10]],
]);

test('a preset curation is rendered at the preset size', function () {
    Curation::presets([CurationPreset::make('Square')->width(30)->height(30)->format('png')]);

    $image = imagickBaselineCurate(imagickBaselineQuadrantsMedia(), ['key' => 'square', 'x' => 10, 'y' => 0, 'width' => 20, 'height' => 20]);

    expect(imagickBaselineSize($image))->toBe([30, 30]);

    imagickBaselineAssertColors(imagickBaselineQuadrants($image), [IMAGICK_BASELINE_RED, IMAGICK_BASELINE_GREEN, IMAGICK_BASELINE_BLUE, IMAGICK_BASELINE_YELLOW], 0);
});

test('a preset crop that overhangs the image keeps its shape and pads the overhang white', function () {
    Curation::presets([CurationPreset::make('Wide')->width(190)->height(100)->format('png')]);

    // A black 100x200 PNG; the box reaches 45px past each side of it.
    $source = imagecreatetruecolor(100, 200);
    ob_start();
    imagepng($source);
    Storage::disk('public')->put('media/stripe.png', ob_get_clean());
    $media = makeMedia(['directory' => 'media', 'name' => 'stripe', 'path' => 'media/stripe.png', 'ext' => 'png', 'type' => 'image/png', 'width' => 100, 'height' => 200]);

    $image = imagickBaselineCurate($media, ['key' => 'wide', 'x' => -45, 'y' => 50, 'width' => 190, 'height' => 100]);

    expect(imagickBaselineSize($image))->toBe([190, 100]);

    imagickBaselineAssertColors(
        array_map(fn (array $point): array => imagickBaselinePixel($image, ...$point), [[0, 50], [44, 50], [45, 50], [144, 50], [145, 50], [189, 50]]),
        [[255, 255, 255], [255, 255, 255], [0, 0, 0], [0, 0, 0], [255, 255, 255], [255, 255, 255]],
        0,
    );
});
