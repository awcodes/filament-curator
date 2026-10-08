<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\CuratorUtils;
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

function detectedUploadTypeSourceFile(string $name, string $content): string
{
    $directory = sys_get_temp_dir() . '/curator-detected-type-' . bin2hex(random_bytes(6));
    mkdir($directory);
    file_put_contents($directory . '/' . $name, $content);

    return $directory . '/' . $name;
}

test('html declared as an image is rejected', function (string $name) {
    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent($name, '<html><body><p>page</p></body></html>')->mimeType('image/png'))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
})->with([
    'html name' => 'page.html',
    'image name' => 'page.png',
]);

test('html declared as an image is rejected by the picker panel', function () {
    Livewire::test(CuratorPanel::class, ['settings' => [
        'acceptedFileTypes' => ['image/jpeg', 'image/png'],
        'diskName' => 'public',
        'directory' => 'uploads',
        'visibility' => 'public',
        'isMultiple' => true,
        'rules' => [],
        'statePath' => 'data.media',
    ]])
        ->set('panelData.files_to_add', [UploadedFile::fake()->createWithContent('page.png', '<html><body><p>page</p></body></html>')->mimeType('image/png')])
        ->callAction('addFiles')
        ->assertHasFormErrors(['files_to_add']);

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

test('accepted types are matched against the detected type, with wildcards', function (array $acceptedTypes) {
    Curator::acceptedFileTypes($acceptedTypes);

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('photo.jpg', detectedUploadTypeJpeg())->mimeType('application/pdf'))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Media::query()->sole()->type)->toBe('image/jpeg');
})->with([
    'exact type' => [['image/jpeg']],
    'wildcard' => [['image/*']],
]);

test('a php extension is rejected unless php is accepted', function () {
    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('photo.php', detectedUploadTypeJpeg())->mimeType('image/jpeg'))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0);
});

test('an svg with a leading comment and a script is stored as a sanitized svg whatever type is declared', function (string $declaredType) {
    $content = "<!-- icon -->\n<svg xmlns=\"http://www.w3.org/2000/svg\"><script>alert(1)</script><rect width=\"1\" height=\"1\"/></svg>";

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('icon.svg', $content)->mimeType($declaredType))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('svg')
        ->and($media->type)->toBe('image/svg+xml')
        ->and(Storage::disk('public')->get($media->path))->not->toContain('<script')->toContain('rect');
})->with([
    'declared as detected' => 'text/html',
    'declared as svg' => 'image/svg+xml',
]);

test('imported svg with a leading comment and a script is stored as a sanitized svg', function () {
    $data = CuratorUtils::importMedia(detectedUploadTypeSourceFile('icon.svg', "<!-- icon -->\n<svg xmlns=\"http://www.w3.org/2000/svg\"><script>alert(1)</script><rect/></svg>"), disk: 'public');

    expect($data['ext'])->toBe('svg')
        ->and($data['type'])->toBe('image/svg+xml')
        ->and(Storage::disk('public')->get($data['path']))->not->toContain('<script')->toContain('rect');
});

test('imported html named svg is not stored as svg', function () {
    $data = CuratorUtils::importMedia(detectedUploadTypeSourceFile('icon.svg', "<!-- icon -->\n<html><body><script>alert(1)</script></body></html>"), disk: 'public');

    expect($data['ext'])->not->toBe('svg')
        ->and($data['type'])->toBe('text/html');
});

test('repair-extensions keeps an svg with a leading comment and neutralises html named svg', function () {
    Storage::disk('public')->put('media/icon.svg', "<!-- icon -->\n<svg xmlns=\"http://www.w3.org/2000/svg\"><script>alert(1)</script><rect/></svg>");
    Storage::disk('public')->put('media/page.svg', "<!-- page -->\n<html><body><script>alert(1)</script></body></html>");

    $icon = makeMedia(['directory' => 'media', 'name' => 'icon', 'path' => 'media/icon.svg', 'ext' => 'svg', 'type' => 'image/svg+xml']);
    $page = makeMedia(['directory' => 'media', 'name' => 'page', 'path' => 'media/page.svg', 'ext' => 'svg', 'type' => 'image/svg+xml']);

    $this->artisan('curator:repair-extensions')->assertSuccessful();

    expect($icon->refresh()->path)->toBe('media/icon.svg')
        ->and($page->refresh()->path)->toBe('media/page.txt')
        ->and($page->type)->toBe('text/plain');
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

test('detectFromContents detects from the first 64 KiB', function () {
    expect(MimeType::detectFromContents(detectedUploadTypeJpeg() . str_repeat("\0", 128 * 1024)))->toBe('image/jpeg')
        ->and(MimeType::detectFromContents(''))->toBe('application/octet-stream');
});

test('isAccepted matches exact types and wildcards', function (string $type, array $accepted, bool $expected) {
    expect(MimeType::isAccepted($type, $accepted))->toBe($expected);
})->with([
    ['image/png', ['image/png'], true],
    ['image/png', ['image/*'], true],
    ['text/html', ['image/*'], false],
    ['text/html', ['image/png', 'application/pdf'], false],
    ['image/png', [], false],
]);
