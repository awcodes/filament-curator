<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Awcodes\Curator\Resources\Media\Pages\EditMedia;
use Awcodes\Curator\Resources\Media\Pages\ListMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

/**
 * The uploader's state comes from the client. Only Livewire's temporary
 * uploads may become media; an array naming an existing file must not.
 */
beforeEach(function () {
    config(['curator.default_disk' => 'public']);
    Storage::fake('public');
    Storage::disk('public')->put('private/secret.pdf', "%PDF-1.4\n");
});

function forgedUploaderStateEntry(): array
{
    return [
        'disk' => 'public',
        'directory' => 'private',
        'visibility' => 'public',
        'name' => 'secret',
        'path' => 'private/secret.pdf',
        'size' => 9,
        'type' => 'application/pdf',
        'ext' => 'pdf',
    ];
}

test('a forged entry on the create page does not become media', function () {
    Livewire::test(CreateMedia::class)
        ->set('data.file', ['forged' => forgedUploaderStateEntry()])
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0);
});

test('a forged entry in the multi upload action does not become media', function () {
    Livewire::test(ListMedia::class)
        ->callAction('curator_multi_upload', data: ['files' => ['forged' => forgedUploaderStateEntry()]])
        ->assertHasActionErrors(['files']);

    expect(Media::query()->count())->toBe(0);
});

test('the uploader drops state that is not a fresh upload when saving', function () {
    $component = Livewire::test(CreateMedia::class)->instance();
    $uploader = $component->form->getComponent('file', withHidden: true);

    $uploader->rawState(['forged' => forgedUploaderStateEntry()]);
    $uploader->saveUploadedFiles();

    expect($uploader->getRawState())->toBe([]);
});

test('replacing a file from the edit page still works', function () {
    Storage::disk('public')->put('photo.jpg', 'old');
    $media = makeMedia(['name' => 'photo', 'path' => 'photo.jpg']);

    Livewire::test(EditMedia::class, ['record' => $media->id])
        ->set('data.file', UploadedFile::fake()->image('new.png', 30, 10))
        ->call('save')
        ->assertHasNoFormErrors();

    // 4.x's file swap keeps a leading slash for media at the disk root.
    expect($media->refresh()->path)->toBeIn(['photo.png', '/photo.png'])
        ->and($media->width)->toBe(30)
        ->and(Storage::disk('public')->exists('photo.jpg'))->toBeFalse();
});

test('a forged entry on the edit page does not replace the file', function () {
    Storage::disk('public')->put('photo.jpg', 'old');
    $media = makeMedia(['name' => 'photo', 'path' => 'photo.jpg']);

    Livewire::test(EditMedia::class, ['record' => $media->id])
        ->set('data.file', ['forged' => forgedUploaderStateEntry()])
        ->call('save')
        ->assertHasFormErrors(['file']);

    expect($media->refresh()->path)->toBe('photo.jpg')
        ->and(Storage::disk('public')->exists('private/secret.pdf'))->toBeTrue();
});
