<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaManagementDeniedPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

// The panel's per-item actions (edit / delete / download) receive the target
// record id — and previously the disk/path — as client-supplied Livewire action
// arguments. These tests lock in that the actions honour the host application's
// MediaPolicy and never trust the client-supplied disk/path.

test('destroyItem deletes the record when no policy is registered', function () {
    Storage::fake('public');

    $media = makeMedia(['name' => 'deletable', 'directory' => 'uploads']);

    Livewire::test(CuratorPanel::class)
        ->mountAction('destroyItem', ['item' => ['id' => $media->id]])
        ->callMountedAction();

    expect(Media::find($media->id))->toBeNull();
});

test('destroyItem is blocked by a denying delete policy', function () {
    Storage::fake('public');

    Gate::policy(Media::class, MediaManagementDeniedPolicy::class);

    $media = makeMedia(['name' => 'protected']);

    Livewire::test(CuratorPanel::class)
        ->mountAction('destroyItem', ['item' => ['id' => $media->id]])
        ->callMountedAction();

    expect(Media::find($media->id))->not->toBeNull();
});

test('editItem updates the record when no policy is registered', function () {
    Storage::fake('public');

    $media = makeMedia(['name' => 'editable', 'alt' => 'original']);

    Livewire::test(CuratorPanel::class)
        ->callAction(
            'editItem',
            data: ['name' => 'editable', 'alt' => 'updated alt'],
            arguments: ['item' => ['id' => $media->id]],
        );

    expect($media->fresh()->alt)->toBe('updated alt');
});

test('editItem is blocked by a denying update policy', function () {
    Storage::fake('public');

    Gate::policy(Media::class, MediaManagementDeniedPolicy::class);

    $media = makeMedia(['name' => 'locked', 'alt' => 'original']);

    Livewire::test(CuratorPanel::class)
        ->callAction(
            'editItem',
            data: ['name' => 'locked', 'alt' => 'updated alt'],
            arguments: ['item' => ['id' => $media->id]],
        );

    expect($media->fresh()->alt)->toBe('original');
});

test('editItem does not prefill the form for an unauthorized record', function () {
    Storage::fake('public');

    Gate::policy(Media::class, MediaManagementDeniedPolicy::class);

    $media = makeMedia(['name' => 'secret', 'alt' => 'do-not-leak']);

    // fillForm returns an empty array for an unauthorized record, so the form is
    // never populated with the protected record's data (e.g. its `alt` text).
    Livewire::test(CuratorPanel::class)
        ->mountAction('editItem', ['item' => ['id' => $media->id]])
        ->assertActionDataSet(['alt' => null]);
});

test('downloadItem streams the record file when authorized', function () {
    Storage::fake('public');
    Storage::disk('public')->put('authorized-file.jpg', 'file-contents');

    $media = makeMedia(['name' => 'authorized-file', 'path' => 'authorized-file.jpg']);

    Livewire::test(CuratorPanel::class)
        ->mountAction('downloadItem', ['item' => ['id' => $media->id]])
        ->callMountedAction()
        ->assertFileDownloaded('authorized-file.jpg');
});

test('downloadItem is blocked by a denying view policy', function () {
    Storage::fake('public');
    Storage::disk('public')->put('view-denied.jpg', 'file-contents');

    Gate::policy(Media::class, MediaManagementDeniedPolicy::class);

    $media = makeMedia(['name' => 'view-denied', 'path' => 'view-denied.jpg']);

    Livewire::test(CuratorPanel::class)
        ->mountAction('downloadItem', ['item' => ['id' => $media->id]])
        ->callMountedAction()
        ->assertNoFileDownloaded();
});

test('downloadItem ignores client-supplied disk and path and uses the record', function () {
    Storage::fake('public');
    Storage::disk('public')->put('real-file.jpg', 'real-contents');

    $media = makeMedia(['name' => 'real-file', 'path' => 'real-file.jpg']);

    // The tampered disk/path point at a different, non-existent location. If the
    // action trusted them it would attempt to read 'evil.txt' from the 'local'
    // disk; instead it must resolve disk/path from the authorized record.
    Livewire::test(CuratorPanel::class)
        ->mountAction('downloadItem', ['item' => [
            'id' => $media->id,
            'disk' => 'local',
            'path' => 'evil.txt',
        ]])
        ->callMountedAction()
        ->assertFileDownloaded('real-file.jpg');
});
