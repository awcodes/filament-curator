<?php

declare(strict_types=1);

use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

/**
 * Livewire releases before 3.8.6 and 4.4.2 take a temporary upload's type from
 * storage metadata, which on an S3 temporary disk is the Content-Type the
 * browser declared. Livewire's test fakes report a declared type in the same
 * way, so these uploads declare a type their bytes do not have.
 */
beforeEach(function () {
    config(['curator.default_disk' => 'public']);
    Storage::fake('public');
});

function detectedUploadTypeJpeg(): string
{
    $image = imagecreatetruecolor(20, 10);
    ob_start();
    imagejpeg($image);
    imagedestroy($image);

    return (string) ob_get_clean();
}

test('html declared as an image is rejected', function () {
    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('page.html', '<html><body><p>page</p></body></html>')->mimeType('image/png'))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

test('the stored type and extension come from the bytes, not the declared type', function () {
    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('photo.png', detectedUploadTypeJpeg())->mimeType('image/png'))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->type)->toBe('image/jpeg')
        ->and($media->ext)->toBe('jpg')
        ->and($media->path)->toEndWith('.jpg');
});

test('accepted types limited to images reject other content declared as an image', function () {
    Curator::acceptedFileTypes(['image/*']);

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('report.png', "%PDF-1.4\n")->mimeType('image/png'))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0);
});

test('detectFromStream reads the bytes and closes the stream', function () {
    $stream = fopen('php://memory', 'r+');
    fwrite($stream, detectedUploadTypeJpeg());
    rewind($stream);

    expect(MimeType::detectFromStream($stream))->toBe('image/jpeg')
        ->and(is_resource($stream))->toBeFalse();
});

test('detectFromStream falls back to octet-stream', function (mixed $stream) {
    expect(MimeType::detectFromStream($stream))->toBe('application/octet-stream');
})->with([
    'no stream' => [null],
    'empty stream' => fn () => fopen('php://memory', 'r'),
]);

test('isAccepted matches exact types and wildcards', function (string $type, array $accepted, bool $expected) {
    expect(MimeType::isAccepted($type, $accepted))->toBe($expected);
})->with([
    ['image/png', ['image/png'], true],
    ['image/png', ['image/*'], true],
    ['text/html', ['image/*'], false],
    ['text/html', ['image/png', 'application/pdf'], false],
]);
