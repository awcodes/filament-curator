<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function uploadPanelSettings(array $overrides = []): array
{
    return array_merge([
        'diskName' => 'public',
        'visibility' => 'public',
        'acceptedFileTypes' => ['image/jpeg', 'image/png'],
        'isMultiple' => true,
    ], $overrides);
}

test('uploads succeed when the panel is mounted without a minimum size', function () {
    Storage::fake('public');

    Livewire::test(CuratorPanel::class, ['settings' => uploadPanelSettings()])
        ->set('panelData.files_to_add', [UploadedFile::fake()->image('photo.jpg', 200, 100)])
        ->callAction('addFiles')
        ->assertHasNoFormErrors();

    expect(Media::query()->where('ext', 'jpg')->count())->toBe(1);
});

test('only the per-file rules of the picker reach the uploader', function () {
    $panel = Livewire::test(CuratorPanel::class, ['settings' => uploadPanelSettings([
        'rules' => ['required', 'array', 'max:3', 'min:1', 'exists:curator,id', 'in:a,b', 'dimensions:min_width=500', 'mimes:jpg'],
    ])]);

    expect($panel->get('validationRules'))->toBe(['dimensions:min_width=500', 'mimes:jpg']);
});

test('the picker rules are applied to uploads', function () {
    Storage::fake('public');

    Livewire::test(CuratorPanel::class, ['settings' => uploadPanelSettings([
        'rules' => ['dimensions:min_width=500'],
    ])])
        ->set('panelData.files_to_add', [UploadedFile::fake()->image('small.jpg', 200, 100)])
        ->callAction('addFiles')
        ->assertHasFormErrors(['files_to_add']);

    expect(Media::query()->count())->toBe(0);
});

test('an upload replaces the selection of a single picker', function () {
    Storage::fake('public');

    $existing = makeMedia(['name' => 'existing']);

    $panel = Livewire::test(CuratorPanel::class, ['settings' => uploadPanelSettings([
        'isMultiple' => false,
        'selected' => [$existing->toArray()],
    ])])
        ->set('panelData.files_to_add', [UploadedFile::fake()->image('photo.jpg', 200, 100)])
        ->callAction('addFiles');

    $selected = $panel->get('selected');

    expect($selected)->toHaveCount(1)
        ->and($selected[0]['id'])->not->toBe($existing->id);
});

test('an upload adds to the selection of a multiple picker', function () {
    Storage::fake('public');

    $existing = makeMedia(['name' => 'existing']);

    $panel = Livewire::test(CuratorPanel::class, ['settings' => uploadPanelSettings([
        'selected' => [$existing->toArray()],
    ])])
        ->set('panelData.files_to_add', [UploadedFile::fake()->image('photo.jpg', 200, 100)])
        ->callAction('addFiles');

    expect($panel->get('selected'))->toHaveCount(2);
});

test('a maximum width rule from the picker rejects wider uploads', function () {
    Storage::fake('public');

    Livewire::test(CuratorPanel::class, ['settings' => uploadPanelSettings([
        'rules' => ['dimensions:max_width=100'],
    ])])
        ->set('panelData.files_to_add', [UploadedFile::fake()->image('wide.jpg', 200, 100)])
        ->callAction('addFiles')
        ->assertHasFormErrors(['files_to_add']);

    expect(Media::query()->count())->toBe(0);
});
