<?php

declare(strict_types=1);

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    config([
        'curator.default_disk' => 'public',
        'filesystems.disks.s3' => [
            'driver' => 's3',
            'root' => 'public',
        ],
        'livewire.temporary_file_upload.disk' => 's3',
    ]);

    Storage::fake('public');
});

test('an image uploaded to a cloud temporary disk is read from that disk', function () {
    expect(Curator::isUsingCloudDisk())->toBeTrue();

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->image('photo.png', 40, 20))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->width)->toBe(40)
        ->and($media->height)->toBe(20);

    Storage::disk('public')->assertExists($media->path);
});
