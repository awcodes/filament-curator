<?php

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Support\MimeType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function jpegBytes(): string
{
    $image = imagecreatetruecolor(8, 8);
    ob_start();
    imagejpeg($image);
    imagedestroy($image);

    return (string) ob_get_clean();
}

/**
 * A fake upload whose reported type is `$claimedType`, the way a type taken
 * from the client (such as a direct upload's content type) would be.
 */
function fakeUpload(string $name, string $contents, string $claimedType): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, $contents)->mimeType($claimedType);
}

function uploadThroughPanel(UploadedFile $file, array $acceptedFileTypes)
{
    return Livewire::test(CuratorPanel::class, [
        'acceptedFileTypes' => $acceptedFileTypes,
        'directory' => 'media',
        'diskName' => 'public',
        'visibility' => 'public',
        'minSize' => 0,
        'maxSize' => 5000,
    ])
        ->set('data.files_to_add', [$file])
        ->callAction('addFiles');
}

beforeEach(function () {
    Storage::fake('public');
});

test('an image uploaded under an html name is stored with the image extension', function () {
    uploadThroughPanel(fakeUpload('x.html', jpegBytes() . '<b>trailer</b>', 'image/jpeg'), ['image/jpeg']);

    $media = Media::sole();

    expect($media->ext)->toBe('jpg')
        ->and($media->path)->toEndWith('.jpg')
        ->and($media->type)->toBe('image/jpeg');

    Storage::disk('public')->assertExists($media->path);
});

test('markup reported as an accepted image type is rejected', function () {
    uploadThroughPanel(fakeUpload('x.html', '<!DOCTYPE html><html><body><b>hello</b></body></html>', 'image/png'), ['image/*'])
        ->assertHasErrors();

    expect(Media::count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

test('characters outside the extension charset never reach the stored path', function () {
    uploadThroughPanel(fakeUpload("photo.png');x('", jpegBytes(), 'image/jpeg'), ['image/jpeg']);

    $media = Media::sole();

    expect($media->ext)->toBe('jpg')
        ->and($media->path)->not->toContain("'");
});

test('a known alias of the detected type is kept', function () {
    uploadThroughPanel(fakeUpload('photo.JPEG', jpegBytes(), 'image/jpeg'), ['image/jpeg']);

    expect(Media::sole()->ext)->toBe('jpeg');
});

test('a csv detected as plain text keeps its extension', function () {
    uploadThroughPanel(fakeUpload('data.csv', "a,b\n1,2\n", 'text/csv'), ['text/csv']);

    expect(Media::sole()->ext)->toBe('csv');
});

test('an svg that starts with a comment keeps its extension and is sanitized', function () {
    $svg = "<!-- logo -->\n<svg xmlns=\"http://www.w3.org/2000/svg\" onload=\"void(0)\"><rect width=\"1\" height=\"1\"/></svg>";

    uploadThroughPanel(fakeUpload('logo.svg', $svg, 'image/svg+xml'), ['image/svg+xml']);

    $media = Media::sole();

    expect($media->ext)->toBe('svg')
        ->and($media->type)->toBe('image/svg+xml')
        ->and(Storage::disk('public')->get($media->path))->not->toContain('onload');
});

test('upload state that is not a fresh upload is rejected', function () {
    Storage::fake('local');
    Storage::disk('local')->put('secret.txt', 'secret');

    Livewire::test(CuratorPanel::class, ['acceptedFileTypes' => ['image/png']])
        ->set('data.files_to_add', [[
            'disk' => 'local', 'directory' => '', 'visibility' => 'public', 'name' => 'secret',
            'path' => 'secret.txt', 'size' => 6, 'type' => 'image/png', 'ext' => 'png',
        ]])
        ->callAction('addFiles')
        ->assertHasErrors();

    expect(Media::count())->toBe(0);
});

test('the copy url handler receives the url as a single javascript string', function () {
    $path = "media/photo.png');toggleMessage();('";
    Storage::disk('public')->put($path, jpegBytes());

    $media = Media::factory()->create(['path' => $path, 'ext' => 'png']);

    $html = view('curator::components.forms.details', ['getRecord' => fn () => $media])->render();

    preg_match('/x-on:click="handleCopy\((.*?)\); toggleMessage\(\);"/s', $html, $matches);

    expect($matches)->toHaveKey(1);

    $argument = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);

    expect($argument)->toMatch("/^'[^']*'$/");
});

test('the stored extension follows the detected type', function (?string $type, ?string $clientExtension, string $expected) {
    expect(MimeType::resolveExtension($type, $clientExtension))->toBe($expected);
})->with([
    ['image/jpeg', 'html', 'jpg'],
    ['image/jpeg', 'JPG', 'jpg'],
    ['image/png', 'php', 'png'],
    ['image/svg+xml', 'svgz', 'svg'],
    ['text/plain', 'md', 'md'],
    ['text/plain', 'html', 'txt'],
    ['text/plain', 'php', 'txt'],
    ['application/x-httpd-php', 'php', 'bin'],
    ['application/octet-stream', 'html', 'bin'],
    [null, 'png', 'bin'],
]);
