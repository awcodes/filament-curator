<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;

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
