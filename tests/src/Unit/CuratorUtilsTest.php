<?php

declare(strict_types=1);

use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\CuratorUtils;
use Illuminate\Http\UploadedFile;

test('local image file import returns expected array keys', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

    $result = CuratorUtils::importMedia(
        path: $file->getPathname(),
        disk: 'public',
        directory: 'test',
        visibility: 'public',
    );

    expect($result)->toHaveKeys([
        'disk', 'directory', 'visibility', 'name', 'path',
        'width', 'height', 'size', 'type', 'ext',
        'alt', 'title', 'description', 'caption', 'exif',
    ]);
});

test('normalizes uppercase extensions to lowercase', function () {
    Storage::fake('public');

    $tmpPath = sys_get_temp_dir().'/curator-test-image.JPG';
    $image = imagecreatetruecolor(100, 100);
    imagejpeg($image, $tmpPath);
    imagedestroy($image);

    $result = CuratorUtils::importMedia(
        path: $tmpPath,
        disk: 'public',
        directory: 'test',
        visibility: 'public',
    );

    expect($result['ext'])->toBe('jpg')
        ->and($result['path'])->toEndWith('.jpg');

    @unlink($tmpPath);
});

test('sets width and height for resizable images', function () {
    Storage::fake('public');

    $tmpPath = sys_get_temp_dir().'/curator-test-image.jpg';
    $image = imagecreatetruecolor(200, 150);
    imagejpeg($image, $tmpPath);
    imagedestroy($image);

    $result = CuratorUtils::importMedia(
        path: $tmpPath,
        disk: 'public',
        directory: 'test',
        visibility: 'public',
    );

    @unlink($tmpPath);

    expect($result['width'])->toBeInt()->toBeGreaterThan(0)
        ->and($result['height'])->toBeInt()->toBeGreaterThan(0);
});

test('sets width and height to null for non-resizable files', function () {
    Storage::fake('public');

    $tmpPath = sys_get_temp_dir().'/curator-test-doc.pdf';
    file_put_contents($tmpPath, 'PDF content');

    $result = CuratorUtils::importMedia(
        path: $tmpPath,
        disk: 'public',
        directory: 'test',
        visibility: 'public',
    );

    @unlink($tmpPath);

    expect($result['width'])->toBeNull()
        ->and($result['height'])->toBeNull();
});

test('generates UUID filename by default', function () {
    Storage::fake('public');

    $tmpPath = sys_get_temp_dir().'/curator-test-uuid.pdf';
    file_put_contents($tmpPath, 'content');

    $result = CuratorUtils::importMedia(
        path: $tmpPath,
        disk: 'public',
        directory: 'test',
        visibility: 'public',
    );

    @unlink($tmpPath);

    expect($result['name'])->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

test('preserves slugified filename when shouldPreserveFilenames is true', function () {
    Storage::fake('public');

    $tmpPath = sys_get_temp_dir().'/my-test-document.pdf';
    file_put_contents($tmpPath, 'content');

    app(CuratorManager::class)->preserveFilenames(true);

    $result = CuratorUtils::importMedia(
        path: $tmpPath,
        disk: 'public',
        directory: 'test',
        visibility: 'public',
    );

    app(CuratorManager::class)->preserveFilenames(false);

    @unlink($tmpPath);

    expect($result['name'])->toBe('my-test-document');
});

test('appends timestamp when file already exists', function () {
    Storage::fake('public');

    $tmpPath = sys_get_temp_dir().'/collision-test-file.pdf';
    file_put_contents($tmpPath, 'content');

    app(CuratorManager::class)->preserveFilenames(true);

    // Pre-create the file to force a collision
    Storage::disk('public')->put('test/collision-test-file.pdf', 'existing');

    $result = CuratorUtils::importMedia(
        path: $tmpPath,
        disk: 'public',
        directory: 'test',
        visibility: 'public',
    );

    app(CuratorManager::class)->preserveFilenames(false);

    @unlink($tmpPath);

    // The name stays the same but the path gets the timestamp appended
    expect($result['path'])->toContain('collision-test-file-')
        ->and($result['name'])->toBe('collision-test-file');
});

test('passes through optional alt title caption description', function () {
    Storage::fake('public');

    $tmpPath = sys_get_temp_dir().'/curator-test-meta.pdf';
    file_put_contents($tmpPath, 'content');

    $result = CuratorUtils::importMedia(
        path: $tmpPath,
        disk: 'public',
        directory: 'test',
        visibility: 'public',
        alt: 'Alt text',
        title: 'My Title',
        caption: 'A caption',
        description: 'A description',
    );

    @unlink($tmpPath);

    expect($result['alt'])->toBe('Alt text')
        ->and($result['title'])->toBe('My Title')
        ->and($result['caption'])->toBe('A caption')
        ->and($result['description'])->toBe('A description');
});
