<?php

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaRecordDeniedPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

// The panel's per-item actions (delete / download / update) resolve the target
// record from client-supplied state (action arguments or the `selected`
// property). These tests lock in that the actions enforce the model's policy
// per-record and never trust a client-supplied disk/path.
//
// MediaRecordDeniedPolicy allows the empty instance the pre-existing global
// ->visible() guard checks, but denies persisted records — so a passing test
// proves the per-record enforcement (not just the global guard) is doing the work.

test('destroy deletes the record when no policy is registered', function () {
    Storage::fake('public');

    $media = Media::factory()->create();

    Livewire::test(CuratorPanel::class)
        ->mountAction('destroy', ['item' => ['id' => $media->id]])
        ->callMountedAction();

    expect(Media::find($media->id))->toBeNull();
});

test('destroy is blocked for a persisted record by a per-record policy', function () {
    Storage::fake('public');

    Gate::policy(Media::class, MediaRecordDeniedPolicy::class);

    $media = Media::factory()->create();

    // callAction asserts the action is visible first — proving the pre-existing
    // global authorize() guard passes (for the empty guard instance) and that it
    // is the per-record check inside the action that refuses to delete.
    Livewire::test(CuratorPanel::class)
        ->callAction('destroy', arguments: ['item' => ['id' => $media->id]]);

    expect(Media::find($media->id))->not->toBeNull();
});

test('download streams the record file when authorized', function () {
    Storage::fake('public');

    $media = Media::factory()->create();

    Livewire::test(CuratorPanel::class)
        ->mountAction('download', ['item' => ['id' => $media->id]])
        ->callMountedAction()
        ->assertFileDownloaded(basename($media->path));
});

test('download ignores client-supplied disk and path and uses the record', function () {
    Storage::fake('public');

    $media = Media::factory()->create();

    // The tampered disk/path point at a different, non-existent location. If the
    // action trusted them it would try to read 'evil.txt' from the 'local' disk;
    // instead it must resolve disk/path from the authorized record.
    Livewire::test(CuratorPanel::class)
        ->mountAction('download', ['item' => [
            'id' => $media->id,
            'disk' => 'local',
            'path' => 'evil.txt',
        ]])
        ->callMountedAction()
        ->assertFileDownloaded(basename($media->path));
});

test('download is blocked for a persisted record by a per-record view policy', function () {
    Storage::fake('public');

    Gate::policy(Media::class, MediaRecordDeniedPolicy::class);

    $media = Media::factory()->create();

    // The action's global 'download' guard passes (policy grants download), so a
    // blocked download proves the per-record 'view' check inside the action works.
    Livewire::test(CuratorPanel::class)
        ->callAction('download', arguments: ['item' => ['id' => $media->id]])
        ->assertNoFileDownloaded();
});

test('updateFile updates the selected record when no policy is registered', function () {
    Storage::fake('public');

    $media = Media::factory()->create(['alt' => 'original']);

    Livewire::test(CuratorPanel::class)
        ->set('selected', [$media->toArray()])
        ->set('data.name', $media->name)
        ->set('data.alt', 'updated alt')
        ->callAction('updateFile');

    expect($media->fresh()->alt)->toBe('updated alt');
});

test('updateFile is blocked for a persisted record by a per-record policy', function () {
    Storage::fake('public');

    Gate::policy(Media::class, MediaRecordDeniedPolicy::class);

    $media = Media::factory()->create(['alt' => 'original']);

    Livewire::test(CuratorPanel::class)
        ->set('selected', [$media->toArray()])
        ->set('data.name', $media->name)
        ->set('data.alt', 'updated alt')
        ->callAction('updateFile');

    expect($media->fresh()->alt)->toBe('original');
});
