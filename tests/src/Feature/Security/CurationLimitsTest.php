<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorCuration;
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Facades\Curation;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaCreateAllowedPolicy;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaManagementDeniedPolicy;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaUpdateAllowedPolicy;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Workbench\App\Models\User;

// A 200×100 PNG, so a padded result is distinguishable from a clamped one by its size alone.
function limitsMedia(): Media
{
    Storage::fake('public');

    $image = imagecreatetruecolor(200, 100);
    ob_start();
    imagepng($image);
    Storage::disk('public')->put('media/limits.png', ob_get_clean());

    return makeMedia(['name' => 'limits', 'directory' => 'media', 'path' => 'media/limits.png', 'ext' => 'png', 'type' => 'image/png', 'width' => 200, 'height' => 100]);
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

function limitsPayload(array $overrides = []): array
{
    return array_replace([
        'key' => 'custom-crop',
        'format' => 'png',
        'quality' => 60,
        'width' => 200,
        'height' => 100,
        'x' => 0,
        'y' => 0,
        'rotate' => 0,
        'scaleX' => 1,
        'scaleY' => 1,
        'canvasData' => ['width' => 500, 'height' => 250, 'naturalWidth' => 200, 'naturalHeight' => 100],
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

test('a crop larger than the image is trimmed to the image', function () {
    limitsComponent()
        ->call('saveCuration', limitsPayload(['width' => 3000, 'height' => 3000]))
        ->assertHasNoErrors();

    expect(savedSize())->toBe([200, 100]);
});

test('a crop that starts before the image is trimmed to the part that overlaps it', function () {
    limitsComponent()
        ->call('saveCuration', limitsPayload(['x' => -50, 'y' => -40, 'width' => 100, 'height' => 100]))
        ->assertHasNoErrors();

    expect(savedSize())->toBe([50, 60]);
});

test('a crop that misses the image entirely is rejected', function () {
    limitsComponent()
        ->call('saveCuration', limitsPayload(['x' => 500, 'y' => 0, 'width' => 100, 'height' => 100]))
        ->assertHasErrors('width')
        ->assertNotDispatched('add-curation');

    expect(Storage::disk('public')->exists('media/limits/custom-crop.png'))->toBeFalse();
});

test('a rotated crop is trimmed to the rotated image', function () {
    limitsComponent()
        ->call('saveCuration', limitsPayload(['rotate' => 90, 'width' => 400, 'height' => 400]))
        ->assertHasNoErrors();

    expect(savedSize())->toBe([100, 200]);
});

test('a preset key with an oversized crop is still saved at the preset size', function () {
    Curation::presets([
        CurationPreset::make('Banner')->width(160)->height(40)->format('png'),
    ]);

    limitsComponent()
        ->call('saveCuration', limitsPayload(['key' => 'banner', 'width' => 3000, 'height' => 750]))
        ->assertHasNoErrors();

    expect(savedSize('banner'))->toBe([160, 40]);
});

// A white 100×200 PNG with a black stripe at x 45–54, narrower than any landscape preset's shape.
function stripeMedia(): Media
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

/**
 * @return array{0: int, 1: int} the first dark column on the row and how many dark columns follow it
 */
function darkRun(string $key, int $y): array
{
    $image = imagecreatefromstring(Storage::disk('public')->get("media/limits/{$key}.png"));
    $start = null;
    $length = 0;

    for ($x = 0; $x < imagesx($image); $x++) {
        ['red' => $red] = imagecolorsforindex($image, imagecolorat($image, $x, $y));

        if ($red < 128) {
            $start ??= $x;
            $length++;
        }
    }

    return [$start, $length];
}

test('a preset crop that overhangs the image keeps its shape', function (int $presetWidth, int $presetHeight, array $expectedRun) {
    Curation::presets([
        CurationPreset::make('Wide')->width($presetWidth)->height($presetHeight)->format('png'),
    ]);

    // The cropper centres a 190×100 box over the container, so it reaches 45px past each side of the image.
    limitsComponent(stripeMedia())
        ->call('saveCuration', limitsPayload(['key' => 'wide', 'x' => -45, 'y' => 50, 'width' => 190, 'height' => 100]))
        ->assertHasNoErrors();

    [$start, $length] = darkRun('wide', intdiv($presetHeight, 2));

    // Resampling softens the stripe's edges by a pixel when it's scaled; a stretched stripe would be ~1.9× wider.
    expect(savedSize('wide'))->toBe([$presetWidth, $presetHeight])
        ->and($start)->toBeGreaterThanOrEqual($expectedRun[0] - 1)->toBeLessThanOrEqual($expectedRun[0] + 1)
        ->and($length)->toBeGreaterThanOrEqual($expectedRun[1] - 1)->toBeLessThanOrEqual($expectedRun[1] + 1);
})->with([
    'at the box size' => [190, 100, [90, 10]],
    'scaled down by half' => [95, 50, [45, 5]],
]);

test('a custom curation is scaled down to the configured maximum dimension', function () {
    config(['curator.curation_max_dimension' => 50]);

    limitsComponent()->call('saveCuration', limitsPayload())->assertHasNoErrors();

    expect(savedSize())->toBe([50, 25]);
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

    expect(savedSize())->toBe([200, 100]);
});

test('the media being curated cannot be swapped from the client', function () {
    $component = limitsComponent();
    $other = makeMedia(['name' => 'other', 'path' => 'other.png']);

    expect(fn () => $component->set('media', $other->getKey()))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});
