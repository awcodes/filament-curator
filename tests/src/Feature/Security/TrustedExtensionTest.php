<?php

declare(strict_types=1);

use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\CuratorUtils;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Awcodes\Curator\Resources\Media\Pages\EditMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Js;
use Livewire\Livewire;

/**
 * Curator detects an upload's type from its bytes. Each upload here also
 * declares the type its bytes are detected as, the way current Livewire
 * releases report it, so nothing depends on a declared type.
 */
beforeEach(function () {
    config(['curator.default_disk' => 'public']);
});

function trustedExtensionJpeg(): string
{
    $image = imagecreatetruecolor(20, 10);
    ob_start();
    imagejpeg($image);
    imagedestroy($image);

    return (string) ob_get_clean();
}

function trustedExtensionUpload(string $name, string $content): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, $content)->mimeType((string) (new finfo(FILEINFO_MIME_TYPE))->buffer($content));
}

function trustedExtensionSourceFile(string $name, string $content): string
{
    $directory = sys_get_temp_dir() . '/curator-trusted-extension-' . bin2hex(random_bytes(6));
    mkdir($directory);
    file_put_contents($directory . '/' . $name, $content);

    return $directory . '/' . $name;
}

test('image content uploaded under an html name is stored and served as an image', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload('poly.html', trustedExtensionJpeg() . '<script>alert(1)</script>'))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('jpg')
        ->and($media->path)->toEndWith('.jpg')
        ->and($media->type)->toBe('image/jpeg')
        ->and($media->url)->toEndWith('.jpg')
        ->and(Storage::disk('public')->allFiles())->each->not->toEndWith('.html');

    $response = $this->get(app(GlideManager::class)->getUrl($media->path));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('image/');
});

test('a filename with script in its extension is stored with a safe extension', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload("photo.png');alert(1);('", trustedExtensionJpeg()))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('jpg')
        ->and($media->path)->toMatch('/^[a-z0-9\/-]+\.jpg$/');
});

test('the extension is replaced even when filenames are preserved', function () {
    Storage::fake('public');
    app(CuratorManager::class)->preserveFilenames(true);

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload('Holiday Photo.html', trustedExtensionJpeg()))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Media::query()->sole()->path)->toEndWith('holiday-photo.jpg');
});

test('ordinary uploads keep their extension', function (string $name, string $content, string $type, string $ext) {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload($name, $content))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe($ext)
        ->and($media->path)->toEndWith('.' . $ext)
        ->and($media->type)->toBe($type);
})->with([
    'jpg' => fn (): array => ['photo.jpg', trustedExtensionJpeg(), 'image/jpeg', 'jpg'],
    'jpeg alias' => fn (): array => ['photo.jpeg', trustedExtensionJpeg(), 'image/jpeg', 'jpeg'],
    'uppercase' => fn (): array => ['PHOTO.JPG', trustedExtensionJpeg(), 'image/jpeg', 'jpg'],
    'pdf' => ['report.pdf', "%PDF-1.4\n", 'application/pdf', 'pdf'],
    'csv' => ['data.csv', "a,b\n1,2\n", 'text/csv', 'csv'],
    'markdown detected as plain text' => ['notes.md', "# Notes\n\nSome text.\n", 'text/plain', 'md'],
]);

test('a png upload keeps working', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->image('logo.png', 40, 20))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('png')
        ->and($media->width)->toBe(40);
});

test('svg markup uploaded under an html name is stored as a sanitized svg', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload('logo.html', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><rect width="1" height="1"/></svg>'))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('svg')
        ->and(Storage::disk('public')->get($media->path))->not->toContain('<script')->toContain('rect');
});

test('the copy url button receives the url as an escaped javascript string', function () {
    Storage::fake('public');
    Storage::disk('public')->put("photo.png');alert(1);('", trustedExtensionJpeg());

    $media = makeMedia([
        'name' => 'photo',
        'path' => "photo.png');alert(1);('",
        'ext' => "png');alert(1);('",
    ]);

    Livewire::test(EditMedia::class, ['record' => $media->id])
        ->assertSeeHtml('handleCopy(' . Js::from($media->url)->toHtml() . ')')
        ->assertDontSeeHtml('handleCopy(&#039;');
});

test('imported image content named html is stored as an image', function () {
    Storage::fake('public');

    $data = CuratorUtils::importMedia(trustedExtensionSourceFile('poly.html', trustedExtensionJpeg() . '<script>alert(1)</script>'), disk: 'public');

    expect($data['ext'])->toBe('jpg')
        ->and($data['path'])->toEndWith('.jpg')
        ->and($data['type'])->toBe('image/jpeg');
});

test('an imported filename with script in its extension gets a safe extension', function () {
    Storage::fake('public');

    $data = CuratorUtils::importMedia(trustedExtensionSourceFile("photo.png');alert(1);('", trustedExtensionJpeg()), disk: 'public');

    expect($data['ext'])->toBe('jpg')
        ->and($data['path'])->toMatch('/^[a-z0-9\/-]+\.jpg$/');
});

test('imported files keep an extension that matches their content', function () {
    Storage::fake('public');

    $data = CuratorUtils::importMedia(trustedExtensionSourceFile('report.pdf', "%PDF-1.4\n"), disk: 'public');

    expect($data['ext'])->toBe('pdf')
        ->and($data['type'])->toBe('application/pdf');
});

function trustedExtensionZippedDocx(): string
{
    // An archive whose first entry is not [Content_Types].xml, which libmagic
    // then reports as application/zip.
    $path = tempnam(sys_get_temp_dir(), 'curator-docx');
    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::OVERWRITE);
    $zip->addFromString('docProps/app.xml', '<Properties/>');
    $zip->addFromString('[Content_Types].xml', '<Types/>');
    $zip->addFromString('word/document.xml', '<w:document/>');
    $zip->close();

    $content = (string) file_get_contents($path);
    @unlink($path);

    return $content;
}

test('svg that does not start with its root element is stored as a sanitized svg', function (string $content) {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload('icon.svg', $content))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('svg')
        ->and($media->type)->toBe('image/svg+xml')
        ->and(Storage::disk('public')->get($media->path))->not->toContain('<script')->not->toContain('onload')->toContain('rect');
})->with([
    'leading whitespace, detected as plain text' => "\n  <svg xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"1\" height=\"1\" onload=\"alert(1)\"/></svg>",
    'leading comment, detected as plain text' => "<!-- icon -->\n<svg xmlns=\"http://www.w3.org/2000/svg\"><rect width=\"1\" height=\"1\" onload=\"alert(1)\"/></svg>",
    'leading whitespace, detected as html' => "\n  <svg xmlns=\"http://www.w3.org/2000/svg\"><script>alert(1)</script><rect width=\"1\" height=\"1\"/></svg>",
    'leading comment, detected as html' => "<!-- icon -->\n<svg xmlns=\"http://www.w3.org/2000/svg\"><script>alert(1)</script><rect width=\"1\" height=\"1\"/></svg>",
]);

test('html named svg is not refined to svg', function (string $content) {
    Storage::fake('public');

    expect((new finfo(FILEINFO_MIME_TYPE))->buffer($content))->toBe('text/html');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload('icon.svg', $content))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
})->with([
    'html document' => "<!-- icon -->\n<html><body><script>alert(1)</script></body></html>",
    'svg inside an html root' => "<!-- icon -->\n<html xmlns=\"http://www.w3.org/1999/xhtml\"><body><svg xmlns=\"http://www.w3.org/2000/svg\"><rect/></svg><script>alert(1)</script></body></html>",
    'svg root outside the svg namespace' => "<!-- icon -->\n<svg xmlns=\"http://www.w3.org/1999/xhtml\"><script>alert(1)</script></svg>",
    'svg root that is not well-formed xml' => "<!-- icon -->\n<svg xmlns=\"http://www.w3.org/2000/svg\"><script>alert(1)</script><rect></svg>",
]);

test('text that only mentions svg is not treated as svg', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload('notes.svg', 'see <svg> in the docs'))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Media::query()->sole()->ext)->toBe('txt');
});

test('an office document detected as a zip archive keeps its extension', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload('report.docx', trustedExtensionZippedDocx()))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('docx')
        ->and($media->type)->toBe('application/vnd.openxmlformats-officedocument.wordprocessingml.document');
});

test('a plain zip archive is still stored as zip', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload('archive.html', trustedExtensionPlainZip()))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Media::query()->sole()->ext)->toBe('zip');
});

function trustedExtensionPlainZip(): string
{
    // Newer libmagic recognises Office documents even when [Content_Types].xml
    // isn't the first entry, so a plain archive must hold no Office parts.
    $path = tempnam(sys_get_temp_dir(), 'curator-zip');
    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::OVERWRITE);
    $zip->addFromString('readme.txt', 'hello');
    $zip->close();

    $content = (string) file_get_contents($path);
    @unlink($path);

    return $content;
}

test('imported svg with a leading comment is stored as a sanitized svg', function () {
    Storage::fake('public');

    $data = CuratorUtils::importMedia(trustedExtensionSourceFile('icon.svg', "<!-- icon -->\n<svg xmlns=\"http://www.w3.org/2000/svg\" onload=\"alert(1)\"><rect/></svg>"), disk: 'public');

    expect($data['ext'])->toBe('svg')
        ->and($data['type'])->toBe('image/svg+xml')
        ->and(Storage::disk('public')->get($data['path']))->not->toContain('onload')->toContain('rect');
});

test('an imported office document detected as a zip archive keeps its extension', function () {
    Storage::fake('public');

    $data = CuratorUtils::importMedia(trustedExtensionSourceFile('report.docx', trustedExtensionZippedDocx()), disk: 'public');

    expect($data['ext'])->toBe('docx');
});
