<?php

declare(strict_types=1);

use Awcodes\Curator\Config\GlideManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// A fresh Laravel app sets FILESYSTEM_DISK=local, which curator's default disk
// follows. Uploads there stored without error, then the Glide route 500'd with
// a FileNotFoundException because Glide only ever read storage/app/public.
test('the glide route serves images stored on a non-public disk', function () {
    Storage::fake('local');
    Storage::disk('local')->putFileAs('media', UploadedFile::fake()->image('photo.png', 40, 20), 'photo.png');

    makeMedia([
        'disk' => 'local',
        'directory' => 'media',
        'name' => 'photo',
        'path' => 'media/photo.png',
        'type' => 'image/png',
        'ext' => 'png',
    ]);

    $glide = app(GlideManager::class);

    try {
        $response = $this->get($glide->getUrl('media/photo.png', ['w' => 20]));

        $response->assertOk();

        expect($response->headers->get('Content-Type'))->toBe('image/png');
    } finally {
        $glide->getServer('local')->deleteCache('media/photo.png');
    }
});
