<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\RichEditor\CuratorFileAttachmentProvider;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake(FileUploadConfiguration::disk());
});

function editorUpload(UploadedFile $file): TemporaryUploadedFile
{
    $file->storeAs('/' . FileUploadConfiguration::path(), $name = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded($file), ['disk' => FileUploadConfiguration::disk()]);

    return TemporaryUploadedFile::createFromLivewire($name);
}

test('a media key resolves to its url', function () {
    $media = makeMedia(['path' => 'media/photo.jpg']);

    expect(CuratorFileAttachmentProvider::make()->getFileAttachmentUrl($media->getKey()))->toBe(Storage::disk('public')->url('media/photo.jpg'))
        ->and(CuratorFileAttachmentProvider::make()->getFileAttachmentUrl((string) $media->getKey()))->toBe(Storage::disk('public')->url('media/photo.jpg'));
});

test('private media, missing media and values that are not keys get no url', function (mixed $key) {
    $private = makeMedia(['visibility' => 'private']);

    expect(CuratorFileAttachmentProvider::make()->getFileAttachmentUrl($key === 'private' ? $private->getKey() : $key))->toBeNull();
})->with([
    'private media' => 'private',
    'missing media' => 999,
    'empty string' => '',
    'null' => null,
    'array' => [[1]],
    'path' => 'media/photo.jpg',
]);

test('an upload is added to the library and its key returned', function () {
    $key = CuratorFileAttachmentProvider::make()
        ->directory('editor')
        ->saveUploadedFileAttachment(editorUpload(UploadedFile::fake()->image('Mountain dusk.jpg', 120, 80)));

    $media = Media::query()->findOrFail($key);

    expect($media->disk)->toBe('public')
        ->and($media->visibility)->toBe('public')
        ->and($media->directory)->toBe('editor')
        ->and($media->type)->toBe('image/jpeg')
        ->and($media->title)->toBe('Mountain dusk')
        ->and([$media->width, $media->height])->toBe([120, 80])
        ->and(Storage::disk('public')->exists($media->path))->toBeTrue();
});

test('an upload of a type the library does not accept is refused', function () {
    $provider = CuratorFileAttachmentProvider::make()->acceptedFileTypes(['image/jpeg']);

    expect(fn () => $provider->saveUploadedFileAttachment(editorUpload(UploadedFile::fake()->createWithContent('page.jpg', '<html><script>alert(1)</script></html>'))))
        ->toThrow(ValidationException::class);

    expect(Media::query()->count())->toBe(0);
});

test('an upload larger than the maximum size is refused', function () {
    $provider = CuratorFileAttachmentProvider::make()->maxSize(1);

    expect(fn () => $provider->saveUploadedFileAttachment(editorUpload(UploadedFile::fake()->image('large.jpg', 400, 400)->size(20))))
        ->toThrow(ValidationException::class);

    expect(Media::query()->count())->toBe(0);
});

test('an uploaded SVG is sanitized before it is stored', function () {
    $key = CuratorFileAttachmentProvider::make()
        ->acceptedFileTypes(['image/svg+xml'])
        ->saveUploadedFileAttachment(editorUpload(UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><rect width="1" height="1"/></svg>')));

    $media = Media::query()->findOrFail($key);

    expect(Storage::disk('public')->get($media->path))->not->toContain('<script');
});

test('files no longer in the content are left in the library', function () {
    $media = makeMedia();

    CuratorFileAttachmentProvider::make()->cleanUpFileAttachments(exceptIds: []);

    expect(Media::query()->whereKey($media->getKey())->exists())->toBeTrue();
});
