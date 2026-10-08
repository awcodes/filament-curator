<?php

use Awcodes\Curator\Models\Media;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

function repairJpeg(): string
{
    $image = imagecreatetruecolor(8, 8);
    ob_start();
    imagejpeg($image);
    imagedestroy($image);

    return (string) ob_get_clean();
}

function storedMedia(string $path, string $contents, string $type): Media
{
    Storage::disk('public')->put($path, $contents);

    return Media::factory()->create([
        'disk' => 'public',
        'directory' => 'media',
        'name' => pathinfo($path, PATHINFO_FILENAME),
        'path' => $path,
        'ext' => pathinfo($path, PATHINFO_EXTENSION),
        'type' => $type,
    ]);
}

beforeEach(function () {
    Storage::fake('public');
});

test('a dry run reports the rename without touching files or rows', function () {
    $media = storedMedia('media/poly.html', repairJpeg(), 'image/jpeg');

    $this->artisan('curator:repair-extensions --dry-run')
        ->expectsOutputToContain("would rename: [{$media->id}] media/poly.html -> media/poly.jpg")
        ->assertSuccessful();

    Storage::disk('public')->assertExists('media/poly.html');
    Storage::disk('public')->assertMissing('media/poly.jpg');
    expect($media->fresh()->path)->toBe('media/poly.html');
});

test('image content stored under a document extension is renamed to the image extension', function () {
    $contents = repairJpeg();
    $media = storedMedia('media/poly.html', $contents, 'image/jpeg');

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain("renamed: [{$media->id}] media/poly.html -> media/poly.jpg")
        ->assertSuccessful();

    Storage::disk('public')->assertMissing('media/poly.html');
    expect(Storage::disk('public')->get('media/poly.jpg'))->toBe($contents);

    $media->refresh();

    expect($media->path)->toBe('media/poly.jpg')
        ->and($media->ext)->toBe('jpg')
        ->and($media->type)->toBe('image/jpeg');
});

test('html content is renamed to plain text and kept', function () {
    $contents = '<!DOCTYPE html><html><body><b>hello</b></body></html>';
    $media = storedMedia('media/page.html', $contents, 'text/html');

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain("renamed: [{$media->id}] media/page.html -> media/page.txt")
        ->assertSuccessful();

    expect(Storage::disk('public')->get('media/page.txt'))->toBe($contents)
        ->and($media->fresh()->ext)->toBe('txt');
});

test('html content is left for review when the app accepts html', function () {
    config()->set('curator.accepted_file_types', ['text/html']);

    $media = storedMedia('media/page.html', '<!DOCTYPE html><html><body>hello</body></html>', 'text/html');

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('review')
        ->assertSuccessful();

    Storage::disk('public')->assertExists('media/page.html');
    expect($media->fresh()->path)->toBe('media/page.html');
});

test('an extension that differs only in case is left alone', function () {
    $media = storedMedia('media/photo.JPG', repairJpeg(), 'image/jpeg');

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('mismatch (case only)')
        ->assertSuccessful();

    Storage::disk('public')->assertExists('media/photo.JPG');
    expect($media->fresh()->path)->toBe('media/photo.JPG');
});

test('a matching extension is left alone', function () {
    $media = storedMedia('media/photo.jpg', repairJpeg(), 'image/jpeg');

    $this->artisan('curator:repair-extensions')
        ->doesntExpectOutputToContain('media/photo.jpg ->')
        ->assertSuccessful();

    expect($media->fresh()->path)->toBe('media/photo.jpg');
});

test('the original is kept when the sanitized copy cannot be written', function () {
    $markup = '<svg xmlns="http://www.w3.org/2000/svg"><rect width="1" height="1"/></svg>';
    $media = storedMedia('media/logo.html', $markup, 'text/html');

    $fake = Storage::disk('public');

    Storage::set('public', new class($fake->getDriver(), $fake->getAdapter(), $fake->getConfig()) extends FilesystemAdapter
    {
        public function put($path, $contents, $options = [])
        {
            return false;
        }
    });

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('skipped (could not write)')
        ->assertSuccessful();

    expect(Storage::disk('public')->get('media/logo.html'))->toBe($markup)
        ->and($media->fresh()->path)->toBe('media/logo.html');
});
