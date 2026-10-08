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

    $media = Media::sole();

    expect($media->ext)->toBe('csv')
        ->and($media->type)->toBe('text/csv');
});

test('plain-data text detected as text/plain takes the type of its extension', function (string $extension, string $expected) {
    expect(MimeType::refineDetectedType('text/plain', $extension, fn (): string => '', fn (int $length): string => ''))
        ->toBe($expected)
        ->and(MimeType::resolveExtension($expected, $extension))->toBe(strtolower($extension));
})->with([
    ['csv', 'text/csv'],
    ['CSV', 'text/csv'],
    ['tsv', 'text/tab-separated-values'],
    ['md', 'text/markdown'],
    ['markdown', 'text/markdown'],
    ['ics', 'text/calendar'],
    ['vtt', 'text/vtt'],
]);

test('other extensions on text/plain content are not retyped', function (string $extension) {
    expect(MimeType::refineDetectedType('text/plain', $extension, fn (): string => '', fn (int $length): string => ''))
        ->toBe('text/plain');
})->with(['html', 'js', 'xml', 'svg', 'php', 'txt']);

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

test('an svg upload that cannot be sanitized is rejected and leaves no file', function () {
    $markup = '<?xml version="1.0"?><html xmlns="http://www.w3.org/1999/xhtml"><body><svg/><b>hello</b></body></html>';

    uploadThroughPanel(fakeUpload('x.svg', $markup, 'image/svg+xml'), ['image/svg+xml'])
        ->assertHasErrors();

    expect(Media::count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

test('xml is not accepted through a wildcard', function () {
    $markup = '<?xml version="1.0"?><html xmlns="http://www.w3.org/1999/xhtml"><body>hello</body></html>';

    uploadThroughPanel(fakeUpload('x.xhtml', $markup, 'text/xml'), ['text/*'])
        ->assertHasErrors();

    expect(Media::count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

test('document and script types only match an accepted type listed exactly', function (string $type, array $accepted, bool $expected) {
    expect(MimeType::isAccepted($type, $accepted))->toBe($expected);
})->with([
    ['text/xml', ['text/*'], false],
    ['text/html', ['text/*'], false],
    ['application/xml', ['application/*'], false],
    ['application/xhtml+xml', ['application/*'], false],
    ['application/javascript', ['application/*'], false],
    ['text/html', ['text/html'], true],
    ['image/svg+xml', ['image/*'], true],
    ['text/plain', ['text/*'], true],
]);

test('a legacy office file larger than the sniffed sample keeps its extension', function () {
    // An OLE compound file whose identifying entry lies past the first 64KB.
    $contents = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" . str_repeat("\0", 70000);

    uploadThroughPanel(fakeUpload('report.doc', $contents, 'application/msword'), ['application/msword']);

    $media = Media::sole();

    expect($media->ext)->toBe('doc')
        ->and($media->type)->toBe('application/msword');
});

test('an ole container under a non-office name is not retyped', function () {
    expect(MimeType::refineDetectedType(
        'application/x-ole-storage',
        'html',
        fn (): string => '',
        fn (int $length): string => substr("\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1", 0, $length),
    ))->toBe('application/x-ole-storage');
});

test('an svg named .svg is kept as svg whatever text type libmagic reports', function (string $detected) {
    $svg = "<!-- logo -->\n<svg xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"1\" height=\"1\"/></svg>";

    expect(MimeType::refineDetectedType($detected, 'svg', fn (): string => $svg, fn (int $length): string => substr($svg, 0, $length)))
        ->toBe('image/svg+xml');
})->with(['text/plain', 'text/xml', 'application/xml', 'text/html']);

test('html named .svg is not retyped as svg', function () {
    $html = '<!DOCTYPE html><html><body><svg></svg></body></html>';

    expect(MimeType::refineDetectedType('text/html', 'svg', fn (): string => $html, fn (int $length): string => substr($html, 0, $length)))
        ->toBe('text/html');
});
