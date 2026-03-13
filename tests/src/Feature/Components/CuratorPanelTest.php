<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Models\Media;
use Livewire\Livewire;

function makeMedia(array $overrides = []): Media
{
    return Media::create(array_merge([
        'disk' => 'public',
        'directory' => null,
        'visibility' => 'public',
        'name' => 'test-file',
        'path' => 'test-file.jpg',
        'size' => 1000,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ], $overrides));
}

test('can mount with default settings', function () {
    Storage::fake('public');

    Livewire::test(CuratorPanel::class)
        ->assertSet('isMultiple', false)
        ->assertSet('diskName', 'public')
        ->assertSet('visibility', 'public');
});

test('mounts with settings array applied', function () {
    Storage::fake('public');

    Livewire::test(CuratorPanel::class, [
        'settings' => [
            'isMultiple' => true,
            'diskName' => 'public',
            'directory' => null,
            'visibility' => 'private',
            'acceptedFileTypes' => [],
            'selected' => [],
            'statePath' => 'data.media',
            'shouldPreserveFilenames' => false,
            'isLimitedToDirectory' => false,
            'isTenantAware' => false,
            'tenantOwnershipRelationshipName' => null,
            'defaultSort' => 'desc',
            'rules' => [],
        ],
    ])
        ->assertSet('isMultiple', true)
        ->assertSet('visibility', 'private');
});

test('getFiles returns array of media records', function () {
    Storage::fake('public');

    makeMedia(['name' => 'photo-one']);
    makeMedia(['name' => 'photo-two']);

    $component = Livewire::test(CuratorPanel::class);
    $files = $component->get('files');

    expect($files)->toBeArray()->toHaveCount(2);
});

test('removeFromFiles removes item from files list', function () {
    Storage::fake('public');

    $media = makeMedia(['name' => 'removable']);

    $component = Livewire::test(CuratorPanel::class);

    expect($component->get('files'))->toHaveCount(1);

    $component->call('removeFromFiles', $media->id);

    expect($component->get('files'))->toBeEmpty();
});

test('search update re-filters files list', function () {
    Storage::fake('public');

    makeMedia(['name' => 'matching-photo']);
    makeMedia(['name' => 'other-image']);

    $component = Livewire::test(CuratorPanel::class);

    $component->set('search', 'matching');

    expect($component->get('files'))->toHaveCount(1);
});

test('isMultiple false restricts to single selection indicator', function () {
    Storage::fake('public');

    Livewire::test(CuratorPanel::class, [
        'settings' => ['isMultiple' => false],
    ])
        ->assertSet('isMultiple', false);
});

test('isLimitedToDirectory scopes search to directory', function () {
    Storage::fake('public');

    makeMedia(['name' => 'in-dir', 'directory' => 'uploads']);
    makeMedia(['name' => 'not-in-dir', 'directory' => null]);

    $component = Livewire::test(CuratorPanel::class, [
        'settings' => [
            'isLimitedToDirectory' => true,
            'directory' => 'uploads',
        ],
    ]);

    $component->set('search', 'not-in-dir');

    expect($component->get('files'))->toBeEmpty();
});
