<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorCuration;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Models\User;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaCreateAllowedPolicy;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaManagementDeniedPolicy;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaUpdateAllowedPolicy;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

// A white 100×200 PNG with a black stripe at x 45–54, so where the image lands on the output can be measured.
function limitsMedia(): Media
{
    Storage::fake('public');

    $image = imagecreatetruecolor(100, 200);
    imagefilledrectangle($image, 0, 0, 99, 199, imagecolorallocate($image, 255, 255, 255));
    imagefilledrectangle($image, 45, 0, 54, 199, imagecolorallocate($image, 0, 0, 0));
    ob_start();
    imagepng($image);
    Storage::disk('public')->put('media/limits.png', ob_get_clean());

    return makeMedia(['name' => 'limits', 'directory' => 'media', 'path' => 'media/limits.png', 'ext' => 'png', 'type' => 'image/png', 'width' => 100, 'height' => 200]);
}

function limitsComponent(?Media $media = null): Testable
{
    return Livewire::test(CuratorCuration::class, [
        'media' => $media ?? limitsMedia(),
        'modalId' => 'curation',
        'statePath' => 'data.image',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ]);
}

/**
 * The canvas is shown at the image's natural size unless a test says otherwise, so the output is the crop's size.
 */
function limitsPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'key' => 'custom-crop',
        'format' => 'png',
        'quality' => 60,
        'width' => 100,
        'height' => 200,
        'x' => 0,
        'y' => 0,
        'rotate' => 0,
        'scaleX' => 1,
        'scaleY' => 1,
        'canvasData' => ['width' => 100, 'height' => 200, 'naturalWidth' => 100, 'naturalHeight' => 200],
    ], $overrides);
}

/**
 * @return array{0: int, 1: int}
 */
function savedSize(string $key = 'custom-crop'): array
{
    [$width, $height] = getimagesizefromstring(Storage::disk('public')->get("media/limits/{$key}.png"));

    return [$width, $height];
}

/**
 * @return array{0: int|null, 1: int} the first dark column on the row and how many dark opaque columns follow it; a stripe scaled to a pixel or two blends to grey
 */
function darkRun(int $y, string $key = 'custom-crop'): array
{
    $image = imagecreatefromstring(Storage::disk('public')->get("media/limits/{$key}.png"));
    $start = null;
    $length = 0;

    for ($x = 0; $x < imagesx($image); $x++) {
        ['red' => $red, 'alpha' => $alpha] = imagecolorsforindex($image, imagecolorat($image, $x, $y));

        if ($red < 192 && $alpha < 64) {
            $start ??= $x;
            $length++;
        }
    }

    return [$start, $length];
}

test('an oversized crop is saved on a canvas bounded by the maximum dimension', function () {
    config(['curator.curation_max_dimension' => 300]);

    limitsComponent()
        ->call('saveCuration', limitsPayload(['width' => 3000, 'height' => 3000]))
        ->assertHasNoErrors();

    // The box is scaled by 0.1, so the image fills the top-left 10×20 and the stripe sits at x 4–5.
    [$start, $length] = darkRun(10);

    expect(savedSize())->toBe([300, 300])
        ->and($start)->toBeGreaterThanOrEqual(3)->toBeLessThanOrEqual(5)
        ->and($length)->toBeGreaterThanOrEqual(1)->toBeLessThanOrEqual(2);
});

test('a crop that starts before the image keeps the image at its offset', function () {
    limitsComponent()
        ->call('saveCuration', limitsPayload(['x' => -45, 'y' => 50, 'width' => 190, 'height' => 100]))
        ->assertHasNoErrors();

    [$start, $length] = darkRun(50);

    // Resampling may soften the stripe's edges by a pixel; a stretched stripe would be ~1.9× wider.
    expect(savedSize())->toBe([190, 100])
        ->and($start)->toBeGreaterThanOrEqual(89)->toBeLessThanOrEqual(91)
        ->and($length)->toBeGreaterThanOrEqual(9)->toBeLessThanOrEqual(11);
});

test('the output keeps following the canvas display ratio', function () {
    limitsComponent()
        ->call('saveCuration', limitsPayload(['canvasData' => ['width' => 50, 'height' => 100]]))
        ->assertHasNoErrors();

    expect(savedSize())->toBe([50, 100]);
});

test('a crop that misses the image entirely is rejected', function () {
    limitsComponent()
        ->call('saveCuration', limitsPayload(['x' => 500, 'y' => 0, 'width' => 100, 'height' => 100]))
        ->assertHasErrors('width')
        ->assertNotDispatched('add-curation');

    expect(Storage::disk('public')->exists('media/limits/custom-crop.png'))->toBeFalse();
});

test('a curation is scaled down to the configured maximum dimension', function () {
    config(['curator.curation_max_dimension' => 50]);

    limitsComponent()
        ->call('saveCuration', limitsPayload(['canvasData' => ['width' => 250, 'height' => 500]]))
        ->assertHasNoErrors();

    expect(savedSize())->toBe([25, 50]);
});

test('a canvas ratio cannot inflate the output past the maximum dimension', function () {
    config(['curator.curation_max_dimension' => 64]);

    limitsComponent()
        ->call('saveCuration', limitsPayload(['canvasData' => ['width' => 65535, 'height' => 65535, 'naturalWidth' => 1, 'naturalHeight' => 1]]))
        ->assertHasNoErrors();

    expect(max(savedSize()))->toBe(64);
});

// The other side of each box is kept tiny so that, without validation, nothing large gets allocated.
test('out of range numbers are rejected before the image is touched', function (string $field, array $overrides) {
    limitsComponent()
        ->call('saveCuration', limitsPayload($overrides))
        ->assertHasErrors($field)
        ->assertNotDispatched('add-curation');
})->with([
    'width' => ['width', ['width' => 100000, 'height' => 1]],
    'height' => ['height', ['width' => 1, 'height' => 100000]],
    'x' => ['x', ['x' => 100000, 'width' => 1, 'height' => 1]],
    'y' => ['y', ['y' => -100000, 'width' => 1, 'height' => 1]],
    'rotate' => ['rotate', ['rotate' => 720]],
    'scaleX' => ['scaleX', ['scaleX' => 3]],
    'scaleY' => ['scaleY', ['scaleY' => -0.5]],
    'canvas width' => ['canvasData.width', ['width' => 1, 'height' => 1, 'canvasData' => ['width' => 100000]]],
    'canvas height' => ['canvasData.height', ['width' => 1, 'height' => 1, 'canvasData' => ['height' => 100000]]],
]);

test('a user the policy forbids from updating the media cannot save a curation', function () {
    Gate::policy(Media::class, MediaManagementDeniedPolicy::class);
    $this->actingAs(User::factory()->create());

    limitsComponent()
        ->call('saveCuration', limitsPayload())
        ->assertForbidden()
        ->assertNotDispatched('add-curation');

    expect(Storage::disk('public')->exists('media/limits/custom-crop.png'))->toBeFalse();
});

test('strict authorization without an update policy method refuses the save', function () {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();
    Gate::policy(Media::class, MediaCreateAllowedPolicy::class);
    $this->actingAs(User::factory()->create());

    limitsComponent()
        ->call('saveCuration', limitsPayload())
        ->assertForbidden();

    expect(Storage::disk('public')->exists('media/limits/custom-crop.png'))->toBeFalse();
});

test('a policy without an update method allows the save outside strict mode, as Filament does', function () {
    Gate::policy(Media::class, MediaCreateAllowedPolicy::class);
    $this->actingAs(User::factory()->create());

    limitsComponent()
        ->call('saveCuration', limitsPayload())
        ->assertHasNoErrors()
        ->assertDispatched('add-curation');
});

test('a user the policy allows to update the media can save a curation', function () {
    Gate::policy(Media::class, MediaUpdateAllowedPolicy::class);
    $this->actingAs(User::factory()->create());

    limitsComponent()
        ->call('saveCuration', limitsPayload())
        ->assertHasNoErrors()
        ->assertDispatched('add-curation');

    expect(savedSize())->toBe([100, 200]);
});

test('the media being curated cannot be swapped from the client', function () {
    $component = limitsComponent();
    $other = makeMedia(['name' => 'other', 'path' => 'other.png']);

    expect(fn () => $component->set('media', $other->getKey()))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});
