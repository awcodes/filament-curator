<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;

test('to array', function () {
    Storage::fake('media');

    $record = Media::factory()->create()->fresh();

    expect(array_keys($record->toArray()))
        ->toBe([
            'id',
            'disk',
            'directory',
            'visibility',
            'name',
            'path',
            'width',
            'height',
            'size',
            'type',
            'ext',
            'alt',
            'title',
            'description',
            'caption',
            'pretty_name',
            'exif',
            'curations',
            'tenant_id',
            'created_at',
            'updated_at',
            'url',
            'full_path',
            'thumbnail_url',
            'medium_url',
            'large_url',
        ]);
});

test('the default factory state stores a jpg with its name, extension and dimensions', function () {
    Storage::fake('public');

    $media = Media::factory()->create();

    expect($media->ext)->toBe('jpg')
        ->and($media->path)->toEndWith('.jpg')
        ->and($media->path)->not->toContain(sys_get_temp_dir())
        ->and($media->width)->toBe(1024)
        ->and($media->height)->toBe(576);
});
