<?php

declare(strict_types=1);

use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GlideBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

// Characterization tests for what the Glide route actually serves on GD.
//
// quadrants.png is 40x20 with four flat 20x10 quadrants: red top left, green
// top right, blue bottom left, yellow bottom right. gradient.png is 32x32 with
// red rising along x, green rising along y and blue fixed at 128.
//
// Pixels are compared within a tolerance rather than by hash, so a different
// libjpeg or libwebp build doesn't fail them, but a different crop, scale or
// orientation does.

const GLIDE_BASELINE_RED = [255, 0, 0];
const GLIDE_BASELINE_GREEN = [0, 255, 0];
const GLIDE_BASELINE_BLUE = [0, 0, 255];
const GLIDE_BASELINE_YELLOW = [255, 255, 0];

beforeEach(function () {
    $this->storagePath = sys_get_temp_dir() . '/curator-glide-output-baseline-' . Str::random(12);
    File::ensureDirectoryExists($this->storagePath . '/app');
    $this->app->useStoragePath($this->storagePath);

    Storage::fake('public');
    Storage::fake('local');

    config()->set('curator.glide_token', 'baseline-token');
    config()->set('curator.default_disk', 'public');
    config()->set('curator.temporary_url_expiration', 5);

    $this->travelTo(Carbon::parse('2026-01-01 12:00:00', 'UTC'));

    $fixtures = __DIR__ . '/../../Fixtures/Files/glide';

    foreach (['public', 'local'] as $disk) {
        Storage::disk($disk)->put('media/quadrants.png', File::get($fixtures . '/quadrants.png'));
        Storage::disk($disk)->put('media/gradient.png', File::get($fixtures . '/gradient.png'));
    }
});

afterEach(function () {
    File::deleteDirectory($this->storagePath);
});

function glideOutputBaselineMedia(array $overrides = []): Media
{
    return makeMedia(array_merge([
        'disk' => 'public',
        'directory' => 'media',
        'name' => 'quadrants',
        'path' => 'media/quadrants.png',
        'type' => 'image/png',
        'ext' => 'png',
        'width' => 40,
        'height' => 20,
    ], $overrides));
}

/**
 * @return array{image: GdImage, width: int, height: int}
 */
function glideOutputBaselineDecode(TestResponse $response): array
{
    $image = imagecreatefromstring($response->streamedContent());

    expect($image)->toBeInstanceOf(GdImage::class);

    return ['image' => $image, 'width' => imagesx($image), 'height' => imagesy($image)];
}

/**
 * @return array{0: int, 1: int, 2: int}
 */
function glideOutputBaselinePixel(GdImage $image, int $x, int $y): array
{
    $color = imagecolorat($image, $x, $y);

    return [($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF];
}

/**
 * Samples the centre of each quadrant, in the order top left, top right,
 * bottom left, bottom right.
 *
 * @return array<int, array{0: int, 1: int, 2: int}>
 */
function glideOutputBaselineQuadrants(GdImage $image): array
{
    $width = imagesx($image);
    $height = imagesy($image);

    return array_map(
        fn (array $point): array => glideOutputBaselinePixel($image, (int) ($width * $point[0]), (int) ($height * $point[1])),
        [[0.25, 0.25], [0.75, 0.25], [0.25, 0.75], [0.75, 0.75]],
    );
}

function glideOutputBaselineAssertColors(array $actual, array $expected, int $delta): void
{
    foreach ($expected as $i => $color) {
        foreach ($color as $channel => $value) {
            expect(abs($actual[$i][$channel] - $value))->toBeLessThanOrEqual($delta, "sample {$i} channel {$channel}: " . json_encode($actual[$i]) . ' vs ' . json_encode($color));
        }
    }
}

/**
 * Mean absolute difference per channel between a decoded output and the gradient fixture.
 */
function glideOutputBaselineGradientError(GdImage $image): float
{
    $total = 0;

    for ($x = 0; $x < 32; $x++) {
        for ($y = 0; $y < 32; $y++) {
            [$r, $g, $b] = glideOutputBaselinePixel($image, $x, $y);
            $total += abs($r - $x * 8) + abs($g - $y * 8) + abs($b - 128);
        }
    }

    return $total / (32 * 32 * 3);
}

test('an untransformed png is served as the original png', function () {
    glideOutputBaselineMedia();

    $response = $this->get(Glide::getUrl('media/quadrants.png'));

    $response->assertOk()->assertHeader('Content-Type', 'image/png');

    $decoded = glideOutputBaselineDecode($response);

    expect([$decoded['width'], $decoded['height']])->toBe([40, 20]);

    glideOutputBaselineAssertColors(glideOutputBaselineQuadrants($decoded['image']), [GLIDE_BASELINE_RED, GLIDE_BASELINE_GREEN, GLIDE_BASELINE_BLUE, GLIDE_BASELINE_YELLOW], 0);
});

test('resizing by width, height or both keeps the aspect ratio', function (array $params, array $size) {
    glideOutputBaselineMedia();

    $response = $this->get(Glide::getUrl('media/quadrants.png', $params));

    $response->assertOk()->assertHeader('Content-Type', 'image/png');

    $decoded = glideOutputBaselineDecode($response);

    expect([$decoded['width'], $decoded['height']])->toBe($size);

    glideOutputBaselineAssertColors(glideOutputBaselineQuadrants($decoded['image']), [GLIDE_BASELINE_RED, GLIDE_BASELINE_GREEN, GLIDE_BASELINE_BLUE, GLIDE_BASELINE_YELLOW], 0);
})->with([
    'width' => [['w' => 20], [20, 10]],
    'height' => [['h' => 10], [20, 10]],
    'width and height fit inside the box' => [['w' => 20, 'h' => 20], [20, 10]],
    'upscaled width' => [['w' => 100], [100, 50]],
    'device pixel ratio' => [['w' => 20, 'dpr' => 2], [40, 20]],
]);

test('fit crop fills the box from the centre', function () {
    glideOutputBaselineMedia();

    $response = $this->get(Glide::getUrl('media/quadrants.png', ['w' => 20, 'h' => 20, 'fit' => 'crop']));

    $response->assertOk()->assertHeader('Content-Type', 'image/png');

    $decoded = glideOutputBaselineDecode($response);

    expect([$decoded['width'], $decoded['height']])->toBe([20, 20]);

    glideOutputBaselineAssertColors(glideOutputBaselineQuadrants($decoded['image']), [GLIDE_BASELINE_RED, GLIDE_BASELINE_GREEN, GLIDE_BASELINE_BLUE, GLIDE_BASELINE_YELLOW], 0);
});

test('fit crop with a focal position keeps that side of the image', function () {
    glideOutputBaselineMedia();

    $response = $this->get(Glide::getUrl('media/quadrants.png', ['w' => 10, 'h' => 20, 'fit' => 'crop-left']));

    $response->assertOk();

    $decoded = glideOutputBaselineDecode($response);

    expect([$decoded['width'], $decoded['height']])->toBe([10, 20]);

    glideOutputBaselineAssertColors(glideOutputBaselineQuadrants($decoded['image']), [GLIDE_BASELINE_RED, GLIDE_BASELINE_RED, GLIDE_BASELINE_BLUE, GLIDE_BASELINE_BLUE], 0);
});

test('a manual crop from the glide builder cuts out the given rectangle', function () {
    glideOutputBaselineMedia();

    $response = $this->get(GlideBuilder::make()->crop(20, 10, 20, 10)->toUrl('media/quadrants.png'));

    $response->assertOk();

    $decoded = glideOutputBaselineDecode($response);

    expect([$decoded['width'], $decoded['height']])->toBe([20, 10]);

    glideOutputBaselineAssertColors(glideOutputBaselineQuadrants($decoded['image']), array_fill(0, 4, GLIDE_BASELINE_YELLOW), 0);
});

test('format conversion changes the content type and keeps the pixels', function (string $format, string $contentType, int $delta) {
    glideOutputBaselineMedia();

    $response = $this->get(Glide::getUrl('media/quadrants.png', ['fm' => $format]));

    $response->assertOk()->assertHeader('Content-Type', $contentType);

    $decoded = glideOutputBaselineDecode($response);

    expect([$decoded['width'], $decoded['height']])->toBe([40, 20]);

    glideOutputBaselineAssertColors(glideOutputBaselineQuadrants($decoded['image']), [GLIDE_BASELINE_RED, GLIDE_BASELINE_GREEN, GLIDE_BASELINE_BLUE, GLIDE_BASELINE_YELLOW], $delta);
})->with([
    'webp' => ['webp', 'image/webp', 12],
    'jpg' => ['jpg', 'image/jpeg', 16],
]);

test('the size urls serve webp at their fixed sizes, upscaling small originals', function (string $attribute, array $size) {
    $media = glideOutputBaselineMedia();

    $response = $this->get($media->{$attribute});

    $response->assertOk()->assertHeader('Content-Type', 'image/webp');

    $decoded = glideOutputBaselineDecode($response);

    expect([$decoded['width'], $decoded['height']])->toBe($size);

    glideOutputBaselineAssertColors(glideOutputBaselineQuadrants($decoded['image']), [GLIDE_BASELINE_RED, GLIDE_BASELINE_GREEN, GLIDE_BASELINE_BLUE, GLIDE_BASELINE_YELLOW], 12);
})->with([
    'thumbnail' => ['thumbnail_url', [200, 200]],
    'medium' => ['medium_url', [640, 640]],
    'large' => ['large_url', [1024, 512]],
]);

test('jpeg quality controls how far the output drifts from the original', function () {
    glideOutputBaselineMedia(['name' => 'gradient', 'path' => 'media/gradient.png', 'width' => 32, 'height' => 32]);

    $low = $this->get(Glide::getUrl('media/gradient.png', ['fm' => 'jpg', 'q' => 5]));
    $high = $this->get(Glide::getUrl('media/gradient.png', ['fm' => 'jpg', 'q' => 95]));

    $low->assertOk()->assertHeader('Content-Type', 'image/jpeg');
    $high->assertOk()->assertHeader('Content-Type', 'image/jpeg');

    $lowDecoded = glideOutputBaselineDecode($low);
    $highDecoded = glideOutputBaselineDecode($high);

    expect([$lowDecoded['width'], $lowDecoded['height']])->toBe([32, 32])
        ->and([$highDecoded['width'], $highDecoded['height']])->toBe([32, 32]);

    $lowError = glideOutputBaselineGradientError($lowDecoded['image']);
    $highError = glideOutputBaselineGradientError($highDecoded['image']);

    expect($highError)->toBeLessThan(2.0)
        ->and($lowError)->toBeGreaterThan(4.0)
        ->and(strlen($low->streamedContent()))->toBeLessThan(strlen($high->streamedContent()));
});

test('a transformed image is written to the glide cache path of its disk', function () {
    glideOutputBaselineMedia();
    $private = glideOutputBaselineMedia(['disk' => 'local', 'visibility' => 'private']);

    $this->get(Glide::getUrl('media/quadrants.png', ['w' => 20]))->assertOk();
    $this->get($private->getGlideUrl(['w' => 20]))->assertOk();

    expect(File::files(storage_path('app/.cache/media/quadrants.png'), true))->toHaveCount(1)
        ->and(File::exists(storage_path('app/' . Glide::getServer()->getCachePath('media/quadrants.png', ['w' => 20]))))->toBeTrue()
        ->and(File::files(storage_path('app/.cache/.disks/local-f6a8e84f/media/quadrants.png'), true))->toHaveCount(1)
        ->and(File::exists(storage_path('app/' . Glide::getServer('local')->getCachePath('media/quadrants.png', ['w' => 20]))))->toBeTrue();
});

test('public responses carry a one year public cache-control', function () {
    $media = glideOutputBaselineMedia();

    $response = $this->get($media->thumbnail_url);

    $response->assertOk()->assertHeader('Cache-Control', 'max-age=31536000, public');

    expect($response->headers->has('Expires'))->toBeTrue()
        ->and($response->headers->has('Last-Modified'))->toBeTrue();
});

test('private responses carry a private cache-control that lasts until the url expires', function () {
    $media = glideOutputBaselineMedia(['disk' => 'local', 'visibility' => 'private']);

    $response = $this->get($media->thumbnail_url);

    $response->assertOk()->assertHeader('Cache-Control', 'max-age=300, private');

    expect($response->headers->has('Expires'))->toBeFalse();
});

test('a private response max-age counts down to the rounded expiry', function () {
    $this->travelTo(Carbon::parse('2026-01-01 12:00:30', 'UTC'));
    $media = glideOutputBaselineMedia(['disk' => 'local', 'visibility' => 'private']);
    $url = $media->thumbnail_url;

    $this->get($url)->assertOk()->assertHeader('Cache-Control', 'max-age=330, private');

    $this->travelTo(Carbon::parse('2026-01-01 12:04:00', 'UTC'));

    $this->get($url)->assertOk()->assertHeader('Cache-Control', 'max-age=120, private');
});
