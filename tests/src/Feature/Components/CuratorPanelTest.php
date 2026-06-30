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

test('getFiles returns files from date-based subdirectories when base directory is set', function () {
    Storage::fake('public');

    makeMedia(['name' => 'base-dir-photo', 'directory' => 'uploads/authors']);
    makeMedia(['name' => 'date-subdir-photo', 'directory' => 'uploads/authors/2025/07/15']);
    makeMedia(['name' => 'other-dir-photo', 'directory' => 'other']);

    $component = Livewire::test(CuratorPanel::class, [
        'settings' => ['directory' => 'uploads/authors'],
    ]);

    expect($component->get('files'))
        ->toHaveCount(2)
        ->and(collect($component->get('files'))->pluck('name')->toArray())
        ->toContain('base-dir-photo', 'date-subdir-photo');
});

test('getFiles does not return files from directories that share a prefix', function () {
    Storage::fake('public');

    makeMedia(['name' => 'target', 'directory' => 'uploads/authors']);
    makeMedia(['name' => 'false-match', 'directory' => 'uploads/authors-other']);

    $component = Livewire::test(CuratorPanel::class, [
        'settings' => ['directory' => 'uploads/authors'],
    ]);

    expect($component->get('files'))
        ->toHaveCount(1)
        ->and(collect($component->get('files'))->pluck('name')->toArray())
        ->toContain('target');
});

test('getFiles with null directory only returns files with null directory', function () {
    Storage::fake('public');

    makeMedia(['name' => 'no-dir', 'directory' => null]);
    makeMedia(['name' => 'has-dir', 'directory' => 'uploads']);

    $component = Livewire::test(CuratorPanel::class);

    expect($component->get('files'))
        ->toHaveCount(1)
        ->and(collect($component->get('files'))->pluck('name')->toArray())
        ->toContain('no-dir');
});

test('subdirectory folder icons still appear alongside files from subdirectories', function () {
    Storage::fake('public');

    makeMedia(['name' => 'base-photo', 'directory' => 'uploads/photos']);
    makeMedia(['name' => 'subdir-photo', 'directory' => 'uploads/photos/vacation']);

    $component = Livewire::test(CuratorPanel::class, [
        'settings' => ['directory' => 'uploads/photos'],
    ]);

    expect($component->get('files'))->toHaveCount(2);

    $subDirs = $component->get('subDirectories');
    expect(array_values($subDirs))->toHaveCount(1)
        ->and(array_values($subDirs)[0]['name'])->toBe('vacation');
});

test('navigating into a subdirectory scopes files to that directory and below', function () {
    Storage::fake('public');

    makeMedia(['name' => 'base-photo', 'directory' => 'uploads/photos']);
    makeMedia(['name' => 'vacation-photo', 'directory' => 'uploads/photos/vacation']);
    makeMedia(['name' => 'work-photo', 'directory' => 'uploads/photos/work']);

    $component = Livewire::test(CuratorPanel::class, [
        'settings' => ['directory' => 'uploads/photos'],
    ]);

    expect($component->get('files'))->toHaveCount(3);

    $component->call('handleDirectoryChange', 'uploads/photos/vacation');
    expect($component->get('files'))->toHaveCount(1)
        ->and(collect($component->get('files'))->pluck('name')->toArray())->toContain('vacation-photo');
});

test('deep date-based directory paths synthesize all ancestor folder icons', function () {
    Storage::fake('public');

    makeMedia(['name' => 'date-photo', 'directory' => 'uploads/authors/2025/07/15']);

    $component = Livewire::test(CuratorPanel::class, [
        'settings' => ['directory' => 'uploads/authors'],
    ]);

    // File is visible at the base directory
    expect($component->get('files'))->toHaveCount(1);

    // Immediate child '2025' is present as a folder icon for navigation
    $subDirs = $component->get('subDirectories');
    expect(array_values($subDirs))->toHaveCount(1)
        ->and(array_values($subDirs)[0]['name'])->toBe('2025');
});

test('navigating through deep date path shows correct files and subdirs at each level', function () {
    Storage::fake('public');

    makeMedia(['name' => 'july-photo', 'directory' => 'uploads/authors/2025/07/15']);
    makeMedia(['name' => 'aug-photo', 'directory' => 'uploads/authors/2025/08/01']);

    $component = Livewire::test(CuratorPanel::class, [
        'settings' => ['directory' => 'uploads/authors'],
    ]);

    // Both visible at root
    expect($component->get('files'))->toHaveCount(2);

    // Navigate into 2025 — both still visible, two month subdirs appear
    $component->call('handleDirectoryChange', 'uploads/authors/2025');
    expect($component->get('files'))->toHaveCount(2);
    $subDirs = collect($component->get('subDirectories'))->pluck('name')->sort()->values()->toArray();
    expect($subDirs)->toBe(['07', '08']);

    // Navigate into 2025/07 — only july photo, one day subdir
    $component->call('handleDirectoryChange', 'uploads/authors/2025/07');
    expect($component->get('files'))->toHaveCount(1)
        ->and(collect($component->get('files'))->pluck('name')->toArray())->toContain('july-photo');
    $subDirs = collect($component->get('subDirectories'))->pluck('name')->toArray();
    expect($subDirs)->toBe(['15']);
});

test('mediaId accepts an integer', function () {
    Storage::fake('public');

    $media = makeMedia(['name' => 'int-id-photo']);

    Livewire::test(CuratorPanel::class, [
        'settings' => ['mediaId' => $media->id],
    ])
        ->assertSet('mediaId', $media->id);
});

test('mediaId accepts a UUID string', function () {
    Storage::fake('public');

    $uuid = Illuminate\Support\Str::uuid()->toString();

    Livewire::test(CuratorPanel::class, [
        'settings' => ['mediaId' => $uuid],
    ])
        ->assertSet('mediaId', $uuid);
});

test('mediaId defaults to null when not provided', function () {
    Storage::fake('public');

    Livewire::test(CuratorPanel::class)
        ->assertSet('mediaId', null);
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

test('search does not leak records outside the directory via non-name fields', function () {
    Storage::fake('public');

    makeMedia(['name' => 'in-dir', 'directory' => 'uploads']);
    makeMedia(['name' => 'other', 'directory' => 'secret', 'title' => 'findme']);

    $component = Livewire::test(CuratorPanel::class, [
        'settings' => [
            'isLimitedToDirectory' => true,
            'directory' => 'uploads',
        ],
    ]);

    // Matches on `title` — the unscoped orWhere chain previously broke out of
    // the directory filter and leaked records from other directories.
    $component->set('search', 'findme');

    expect($component->get('files'))->toBeEmpty();
});
