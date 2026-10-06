<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;

test('creating maps file array fields onto model attributes', function () {
    Storage::fake('public');

    $media = Media::create([
        'file' => [
            'disk' => 'public',
            'directory' => 'media',
            'visibility' => 'public',
            'name' => 'test-image',
            'path' => 'media/test-image.jpg',
            'width' => 1024,
            'height' => 768,
            'size' => 1000,
            'type' => 'image/jpeg',
            'ext' => 'jpg',
        ],
    ]);

    expect($media->name)->toBe('test-image')
        ->and($media->ext)->toBe('jpg')
        ->and($media->disk)->toBe('public')
        ->and($media->width)->toBe(1024)
        ->and($media->height)->toBe(768);
});

test('creating unsets the file attribute', function () {
    Storage::fake('public');

    $media = Media::create([
        'file' => [
            'disk' => 'public',
            'directory' => 'media',
            'visibility' => 'public',
            'name' => 'test-image',
            'path' => 'media/test-image.jpg',
            'size' => 1000,
            'type' => 'image/jpeg',
            'ext' => 'jpg',
        ],
    ]);

    expect(isset($media->file))->toBeFalse();
});

test('creating sanitizes exif data', function () {
    Storage::fake('public');

    $media = Media::create([
        'file' => [
            'disk' => 'public',
            'directory' => 'media',
            'visibility' => 'public',
            'name' => 'test-image',
            'path' => 'media/test-image.jpg',
            'size' => 1000,
            'type' => 'image/jpeg',
            'ext' => 'jpg',
            'exif' => ['Make' => 'Canon', 'Model' => 'EOS 5D'],
        ],
    ]);

    expect($media->exif)->toBeArray()
        ->and($media->exif['Make'])->toBe('Canon');
});

test('updating renames file on disk when name is dirty', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/original.jpg', 'content');

    $media = Media::create([
        'disk' => 'public',
        'directory' => 'media',
        'visibility' => 'public',
        'name' => 'original',
        'path' => 'media/original.jpg',
        'size' => 100,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ]);

    $media->name = 'renamed';
    $media->save();

    expect($media->path)->toBe('media/renamed.jpg')
        ->and(Storage::disk('public')->exists('media/renamed.jpg'))->toBeTrue()
        ->and(Storage::disk('public')->exists('media/original.jpg'))->toBeFalse();
});

test('updating appends timestamp when renamed file already exists', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/original.jpg', 'content');
    Storage::disk('public')->put('media/newname.jpg', 'existing-content');

    $media = Media::create([
        'disk' => 'public',
        'directory' => 'media',
        'visibility' => 'public',
        'name' => 'original',
        'path' => 'media/original.jpg',
        'size' => 100,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ]);

    $media->name = 'newname';
    $media->save();

    expect($media->name)->toStartWith('newname-')
        ->and($media->path)->toContain('media/newname-');
});

test('deleted removes file from storage', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/deleteme.jpg', 'content');

    $media = Media::create([
        'disk' => 'public',
        'directory' => 'media',
        'visibility' => 'public',
        'name' => 'deleteme',
        'path' => 'media/deleteme.jpg',
        'size' => 100,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ]);

    $path = $media->path;
    $media->delete();

    expect(Storage::disk('public')->exists($path))->toBeFalse();
});

test('deleted cleans up empty directory', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/deleteme.jpg', 'content');

    $media = Media::create([
        'disk' => 'public',
        'directory' => 'media',
        'visibility' => 'public',
        'name' => 'deleteme',
        'path' => 'media/deleteme.jpg',
        'size' => 100,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ]);

    $media->delete();

    expect(Storage::disk('public')->allFiles('media'))->toBeEmpty();
});

// A null directory is the disk root. Once the disk held no files, deleting the
// last media item used to delete the root itself, empty folders and all.
test('deleted never removes the disk root when the directory is blank', function () {
    Storage::fake('public');
    Storage::disk('public')->put('rootfile.jpg', 'content');
    Storage::disk('public')->makeDirectory('keep-me');

    $media = makeMedia(['directory' => null, 'name' => 'rootfile', 'path' => 'rootfile.jpg']);

    $media->delete();

    expect(Storage::disk('public')->exists('rootfile.jpg'))->toBeFalse()
        ->and(Storage::disk('public')->directoryExists('keep-me'))->toBeTrue()
        ->and(is_dir(Storage::disk('public')->path('')))->toBeTrue();
});

test('deleted removes curations stored beside root level media', function () {
    Storage::fake('public');
    Storage::disk('public')->put('rootfile.jpg', 'content');
    Storage::disk('public')->put('rootfile/thumbnail.jpg', 'content');

    $media = makeMedia(['directory' => null, 'name' => 'rootfile', 'path' => 'rootfile.jpg', 'curations' => [
        ['curation' => ['key' => 'thumbnail', 'path' => 'rootfile/thumbnail.jpg', 'directory' => 'rootfile']],
    ]]);

    $media->delete();

    expect(Storage::disk('public')->directoryExists('rootfile'))->toBeFalse();
});

test('renaming root level media does not store a leading slash in the path', function () {
    Storage::fake('public');
    Storage::disk('public')->put('original.jpg', 'content');

    $media = makeMedia(['directory' => null, 'name' => 'original', 'path' => 'original.jpg']);

    $media->update(['name' => 'renamed']);

    expect($media->fresh()->path)->toBe('renamed.jpg')
        ->and(Storage::disk('public')->exists('renamed.jpg'))->toBeTrue();
});

function curationEntry(string $key, string $path, ?string $directory = null): array
{
    return ['curation' => ['key' => $key, 'disk' => 'public', 'directory' => $directory, 'visibility' => 'public', 'path' => $path]];
}

test('deleted leaves a folder that only shares the media name', function () {
    Storage::fake('public');
    Storage::disk('public')->put('uploads.jpg', 'content');
    Storage::disk('public')->put('uploads/someone-elses.jpg', 'content');

    makeMedia(['directory' => null, 'name' => 'uploads', 'path' => 'uploads.jpg'])->delete();

    expect(Storage::disk('public')->exists('uploads/someone-elses.jpg'))->toBeTrue();
});

test('deleted removes only the curation files it owns', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/photo.jpg', 'content');
    Storage::disk('public')->put('media/photo/thumbnail.webp', 'crop');
    Storage::disk('public')->put('media/photo/unrelated.txt', 'keep');

    makeMedia(['directory' => 'media', 'name' => 'photo', 'path' => 'media/photo.jpg', 'curations' => [
        curationEntry('thumbnail', 'media/photo/thumbnail.webp', 'photo'),
    ]])->delete();

    expect(Storage::disk('public')->exists('media/photo/thumbnail.webp'))->toBeFalse()
        ->and(Storage::disk('public')->exists('media/photo/unrelated.txt'))->toBeTrue();
});

test('renaming moves the curations and updates their paths', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/photo.jpg', 'content');
    Storage::disk('public')->put('media/photo/thumbnail.webp', 'crop');

    $media = makeMedia(['directory' => 'media', 'name' => 'photo', 'path' => 'media/photo.jpg', 'curations' => [
        curationEntry('thumbnail', 'media/photo/thumbnail.webp', 'photo'),
    ]]);

    $media->update(['name' => 'renamed']);
    $media->refresh();

    expect($media->path)->toBe('media/renamed.jpg')
        ->and($media->getCuration('thumbnail')['path'])->toBe('media/renamed/thumbnail.webp')
        ->and($media->getCuration('thumbnail')['directory'])->toBe('renamed')
        ->and(Storage::disk('public')->exists('media/renamed/thumbnail.webp'))->toBeTrue()
        ->and(Storage::disk('public')->directoryExists('media/photo'))->toBeFalse();

    $media->delete();

    expect(Storage::disk('public')->directoryExists('media/renamed'))->toBeFalse();
});

test('swapping keeps the original when the replacement cannot be moved into place', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/photo.jpg', 'original');

    $media = makeMedia(['directory' => 'media', 'name' => 'photo', 'path' => 'media/photo.jpg']);

    $media->file = ['disk' => 'public', 'directory' => 'media', 'name' => 'upload', 'path' => 'livewire-tmp/missing.png', 'ext' => 'png'];

    expect(fn () => $media->save())->toThrow(RuntimeException::class);

    expect(Storage::disk('public')->get('media/photo.jpg'))->toBe('original');
});

test('swapping replaces the original under its name with the new extension', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/photo.jpg', 'original');
    Storage::disk('public')->put('media/upload.png', 'replacement');

    $media = makeMedia(['directory' => 'media', 'name' => 'photo', 'path' => 'media/photo.jpg']);

    $media->file = ['disk' => 'public', 'directory' => 'media', 'name' => 'upload', 'path' => 'media/upload.png', 'ext' => 'png', 'type' => 'image/png'];
    $media->save();
    $media->refresh();

    expect($media->path)->toBe('media/photo.png')
        ->and($media->name)->toBe('photo')
        ->and(Storage::disk('public')->get('media/photo.png'))->toBe('replacement')
        ->and(Storage::disk('public')->exists('media/photo.jpg'))->toBeFalse()
        ->and(Storage::disk('public')->exists('media/upload.png'))->toBeFalse();
});

test('swapping with the same extension overwrites the original in place', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/photo.jpg', 'original');
    Storage::disk('public')->put('media/upload.jpg', 'replacement');

    $media = makeMedia(['directory' => 'media', 'name' => 'photo', 'path' => 'media/photo.jpg']);

    $media->file = ['disk' => 'public', 'directory' => 'media', 'name' => 'upload', 'path' => 'media/upload.jpg', 'ext' => 'jpg'];
    $media->save();

    expect(Storage::disk('public')->get('media/photo.jpg'))->toBe('replacement')
        ->and(Storage::disk('public')->exists('media/upload.jpg'))->toBeFalse();
});

test('swapping moves an upload from another disk on that disk', function () {
    Storage::fake('public');
    Storage::fake('s3');
    Storage::disk('public')->put('media/photo.jpg', 'original');
    Storage::disk('s3')->put('media/upload.jpg', 'replacement');

    $media = makeMedia(['directory' => 'media', 'name' => 'photo', 'path' => 'media/photo.jpg']);

    $media->file = ['disk' => 's3', 'directory' => 'media', 'name' => 'upload', 'path' => 'media/upload.jpg', 'ext' => 'jpg'];
    $media->save();
    $media->refresh();

    expect($media->disk)->toBe('s3')
        ->and(Storage::disk('s3')->get('media/photo.jpg'))->toBe('replacement')
        ->and(Storage::disk('public')->exists('media/photo.jpg'))->toBeFalse();
});
