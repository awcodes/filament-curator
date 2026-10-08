<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

function tamperPanelSettings(array $overrides = []): array
{
    return array_merge([
        'acceptedFileTypes' => ['image/jpeg', 'image/png'],
        'diskName' => 'public',
        'directory' => 'uploads',
        'visibility' => 'public',
        'isMultiple' => true,
        'maxSize' => 1024,
        'rules' => [],
        'statePath' => 'data.media',
    ], $overrides);
}

/**
 * Sends a client update to the panel, as the browser would, and reports whether Livewire refused it.
 */
function attemptClientUpdate(Testable $panel, string $property, mixed $value): bool
{
    try {
        $panel->set($property, $value);
    } catch (CannotUpdateLockedPropertyException) {
        return false;
    }

    return true;
}

function dispatchedInsertMedia(Testable $panel): ?array
{
    $dispatch = collect(data_get($panel->effects, 'dispatches'))->firstWhere('name', 'insert-media');

    return $dispatch['params'][0] ?? null;
}

test('the browser cannot change the panel configuration', function (string $property, mixed $value) {
    Storage::fake('public');

    $panel = Livewire::test(CuratorPanel::class, ['settings' => tamperPanelSettings()]);

    expect(attemptClientUpdate($panel, $property, $value))->toBeFalse();
})->with([
    'settings' => ['settings', ['acceptedFileTypes' => ['text/html']]],
    'a single setting' => ['settings.diskName', 'local'],
    'acceptedFileTypes' => ['acceptedFileTypes', ['text/html']],
    'rules' => ['rules', []],
    'validationRules' => ['validationRules', []],
    'minSize' => ['minSize', 0],
    'maxSize' => ['maxSize', 999999],
    'isMultiple' => ['isMultiple', true],
    'maxItems' => ['maxItems', 1000],
    'diskName' => ['diskName', 'local'],
    'directory' => ['directory', 'elsewhere'],
    'visibility' => ['visibility', 'private'],
    'shouldPreserveFilenames' => ['shouldPreserveFilenames', true],
    'pathGenerator' => ['pathGenerator', 'App\\PathGenerator'],
    'isTenantAware' => ['isTenantAware', false],
    'tenantOwnershipRelationshipName' => ['tenantOwnershipRelationshipName', 'team'],
    'showAll' => ['showAll', true],
    'isLimitedToDirectory' => ['isLimitedToDirectory', false],
    'defaultLimit' => ['defaultLimit', 100000],
    'statePath' => ['statePath', 'data.other'],
    'context' => ['context', 'richEditor'],
    'types' => ['types', ['text/html']],
    'imageResizeTargetWidth' => ['imageResizeTargetWidth', '1'],
    'files' => ['files', []],
    'directories' => ['directories', []],
    'subDirectories' => ['subDirectories', []],
    'breadcrumbs' => ['breadcrumbs', []],
]);

test('the search, upload form and selection still accept client input', function () {
    Storage::fake('public');

    $panel = Livewire::test(CuratorPanel::class, ['settings' => tamperPanelSettings()]);

    expect(attemptClientUpdate($panel, 'search', 'photo'))->toBeTrue()
        ->and(attemptClientUpdate($panel, 'selected', []))->toBeTrue()
        ->and(attemptClientUpdate($panel, 'panelData.files_to_add', []))->toBeTrue();
});

test('an html upload is still rejected after the accepted types were tampered with', function () {
    Storage::fake('public');

    $panel = Livewire::test(CuratorPanel::class, ['settings' => tamperPanelSettings()]);

    attemptClientUpdate($panel, 'acceptedFileTypes', ['text/html']);
    attemptClientUpdate($panel, 'settings', tamperPanelSettings(['acceptedFileTypes' => ['text/html']]));

    $panel
        ->set('panelData.files_to_add', [UploadedFile::fake()->createWithContent('page.html', '<!DOCTYPE html><html><body>page</body></html>')])
        ->callAction('addFiles')
        ->assertHasFormErrors(['files_to_add']);

    expect(Media::query()->count())->toBe(0);
});

test('an upload goes to the configured disk, directory and visibility after tampering', function () {
    Storage::fake('public');
    Storage::fake('local');

    $panel = Livewire::test(CuratorPanel::class, ['settings' => tamperPanelSettings()]);

    attemptClientUpdate($panel, 'diskName', 'local');
    attemptClientUpdate($panel, 'directory', 'curator/elsewhere');
    attemptClientUpdate($panel, 'visibility', 'private');
    attemptClientUpdate($panel, 'shouldPreserveFilenames', true);

    $panel
        ->set('panelData.files_to_add', [UploadedFile::fake()->image('photo.jpg', 20, 20)])
        ->callAction('addFiles')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->disk)->toBe('public')
        ->and($media->directory)->toBe('uploads')
        ->and($media->visibility)->toBe('public')
        ->and($media->name)->not->toBe('photo');
});

test('the size limit still applies after tampering', function () {
    Storage::fake('public');

    $panel = Livewire::test(CuratorPanel::class, ['settings' => tamperPanelSettings(['maxSize' => 1])]);

    attemptClientUpdate($panel, 'maxSize', 999999);

    $panel
        ->set('panelData.files_to_add', [UploadedFile::fake()->image('large.jpg', 20, 20)->size(50)])
        ->callAction('addFiles')
        ->assertHasFormErrors(['files_to_add']);

    expect(Media::query()->count())->toBe(0);
});

test('the browser cannot browse into, and so upload to, a directory that holds no media', function () {
    Storage::fake('public');

    makeMedia(['name' => 'existing', 'directory' => 'uploads/existing']);

    $panel = Livewire::test(CuratorPanel::class, ['settings' => tamperPanelSettings()])
        ->call('handleDirectoryChange', 'curator/elsewhere')
        ->assertSet('directory', 'uploads')
        ->call('handleDirectoryChange', 'uploads/existing')
        ->assertSet('directory', 'uploads/existing')
        ->call('handleDirectoryChange', 'public')
        ->assertSet('directory', null)
        ->call('handleDirectoryChange', 'uploads')
        ->assertSet('directory', 'uploads');

    $panel
        ->call('handleDirectoryChange', 'curator/elsewhere')
        ->set('panelData.files_to_add', [UploadedFile::fake()->image('photo.jpg', 20, 20)])
        ->callAction('addFiles');

    expect(Media::query()->where('name', '!=', 'existing')->sole()->directory)->toBe('uploads');
});

test('inserting media sends the stored records, not the client selection', function () {
    Storage::fake('public');

    $media = makeMedia(['name' => 'stored', 'path' => 'uploads/stored.jpg', 'directory' => 'uploads']);

    $panel = Livewire::test(CuratorPanel::class, ['settings' => tamperPanelSettings()])
        ->set('selected', [
            ['id' => $media->id, 'disk' => 'local', 'path' => 'private/secret.jpg'],
            ['id' => 999999, 'disk' => 'local', 'path' => 'private/other.jpg'],
            ['disk' => 'local', 'path' => 'private/no-id.jpg'],
        ])
        ->callAction('insertMedia');

    $dispatched = dispatchedInsertMedia($panel);

    expect($dispatched)->not->toBeNull()
        ->and($dispatched['statePath'])->toBe('data.media')
        ->and($dispatched['media'])->toHaveCount(1)
        ->and($dispatched['media'][0]['id'])->toBe($media->id)
        ->and($dispatched['media'][0]['disk'])->toBe('public')
        ->and($dispatched['media'][0]['path'])->toBe('uploads/stored.jpg');
});

test('inserting media keeps the selection order', function () {
    Storage::fake('public');

    $first = makeMedia(['name' => 'first']);
    $second = makeMedia(['name' => 'second']);

    $panel = Livewire::test(CuratorPanel::class, ['settings' => tamperPanelSettings()])
        ->set('selected', [$second->toArray(), $first->toArray()])
        ->callAction('insertMedia');

    expect(array_column(dispatchedInsertMedia($panel)['media'], 'id'))->toBe([$second->id, $first->id]);
});

test('a single picker receives one item however many the client selected', function () {
    Storage::fake('public');

    $first = makeMedia(['name' => 'first']);
    $second = makeMedia(['name' => 'second']);

    $panel = Livewire::test(CuratorPanel::class, ['settings' => tamperPanelSettings(['isMultiple' => false])])
        ->set('selected', [$first->toArray(), $second->toArray()])
        ->callAction('insertMedia');

    expect(array_column(dispatchedInsertMedia($panel)['media'], 'id'))->toBe([$first->id]);
});

test('directory names are passed to the browser as javascript strings', function () {
    Storage::fake('public');

    makeMedia(['name' => 'quoted', 'directory' => "o'brien/2025"]);

    Livewire::test(CuratorPanel::class)
        ->assertSeeHtml('handleDirectoryChange(\'o\\u0027brien\')')
        ->assertDontSeeHtml("handleDirectoryChange('o'brien')")
        ->call('handleDirectoryChange', "o'brien/2025")
        ->assertSet('directory', "o'brien/2025")
        ->assertSeeHtml('handleDirectoryChange(\'o\\u0027brien\')')
        ->assertDontSeeHtml("handleDirectoryChange('o'brien')");
});

function glideTestImage(int $width, int $height): string
{
    $image = imagecreatetruecolor($width, $height);

    ob_start();
    imagejpeg($image);
    imagedestroy($image);

    return (string) ob_get_clean();
}

function servedImageSize(string $url): array
{
    $response = test()->get($url);

    $response->assertOk();

    $size = getimagesizefromstring($response->streamedContent());

    return [$size[0], $size[1]];
}

test('the glide route serves media stored under a directory named after its prefix', function () {
    Storage::fake('public');
    Storage::disk('public')->put('curator/x/y.jpg', glideTestImage(40, 20));
    Storage::disk('public')->put('x/y.jpg', glideTestImage(20, 40));

    makeMedia(['directory' => 'curator/x', 'path' => 'curator/x/y.jpg', 'width' => 40, 'height' => 20]);

    $glide = app(GlideManager::class);

    try {
        expect(servedImageSize($glide->getUrl('curator/x/y.jpg', ['fm' => 'jpg'])))->toBe([40, 20]);
    } finally {
        $glide->getServer('public')->deleteCache('curator/x/y.jpg');
    }
});

test('a custom glide server config cannot reintroduce the prefix stripping', function () {
    Storage::fake('public');
    Storage::fake('glide-cache');
    Storage::disk('public')->put('curator/x/y.jpg', glideTestImage(40, 20));
    Storage::disk('public')->put('x/y.jpg', glideTestImage(20, 40));

    makeMedia(['directory' => 'curator/x', 'path' => 'curator/x/y.jpg', 'width' => 40, 'height' => 20]);

    Glide::serverConfig([
        'source' => Storage::disk('public')->getDriver(),
        'cache' => Storage::disk('glide-cache')->getDriver(),
        'cache_path_prefix' => '.cache',
        'base_url' => 'curator',
    ]);

    expect(servedImageSize(app(GlideManager::class)->getUrl('curator/x/y.jpg', ['fm' => 'jpg'])))->toBe([40, 20]);
});
