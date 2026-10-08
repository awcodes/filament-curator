<?php

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Livewire\MediaForm;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaRecordDeniedPolicy;
use FilamentTiptapEditor\FilamentTiptapEditorServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

function pickerDownload(array $items, ?string $uuid = null)
{
    $state = [];

    foreach ($items as $item) {
        $state[(string) Str::uuid()] = $item;
    }

    $component = Livewire::test(MediaForm::class)->set('data.media', $state);

    return $component->callFormComponentAction('media', 'download', arguments: [
        'uuid' => $uuid ?? array_key_first($component->get('data.media')),
    ]);
}

function forgedPickerItem(array $overrides): array
{
    $item = Media::factory()->create()->toArray();

    unset($item['id']);

    return [...$item, ...$overrides];
}

beforeEach(function () {
    $this->app->register(FilamentTiptapEditorServiceProvider::class);

    Storage::fake('public');
    Storage::fake('local');
    Storage::disk('local')->put('secrets/x.txt', 'secret-contents');
});

test('a picker item downloads its own file', function () {
    $media = Media::factory()->create();

    pickerDownload([$media->toArray()])->assertFileDownloaded(basename($media->path));
});

test('a picker item uses the record disk and path, not the state', function () {
    $media = Media::factory()->create();

    pickerDownload([[...$media->toArray(), 'disk' => 'local', 'path' => 'secrets/x.txt']])
        ->assertFileDownloaded(basename($media->path));
});

test('a forged picker item with a made-up id downloads nothing', function () {
    pickerDownload([forgedPickerItem(['id' => 'missing', 'disk' => 'local', 'path' => 'secrets/x.txt'])])
        ->assertNoFileDownloaded();
});

test('a forged picker item with an unknown id downloads nothing', function () {
    pickerDownload([forgedPickerItem(['id' => 999999, 'disk' => 'local', 'path' => 'secrets/x.txt'])])
        ->assertNoFileDownloaded();
});

test('an unknown uuid downloads nothing', function () {
    $media = Media::factory()->create();

    pickerDownload([$media->toArray()], 'missing')->assertNoFileDownloaded();
});

test('the picker download honours a denying view policy', function () {
    Gate::policy(Media::class, MediaRecordDeniedPolicy::class);

    $media = Media::factory()->create();

    pickerDownload([$media->toArray()])->assertNoFileDownloaded();
});
