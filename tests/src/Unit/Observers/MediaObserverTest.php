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
