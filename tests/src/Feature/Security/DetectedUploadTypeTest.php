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
    ['text/html', ['text/html'], true],
    ['text/html', ['text/*'], false],
    ['text/xml', ['text/*'], false],
    ['text/javascript', ['text/*'], false],
    ['application/xml', ['application/*'], false],
    ['application/xhtml+xml', ['application/*'], false],
    ['application/octet-stream', ['application/*'], false],
    ['image/svg+xml', ['image/*'], true],
    ['application/pdf', ['application/*'], true],
    ['text/plain', ['text/*'], true],
]);

test('a wildcard does not accept xhtml', function (string $wildcard, string $declared) {
    Curator::acceptedFileTypes([$wildcard]);

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('page.xml', '<?xml version="1.0"?><html xmlns="http://www.w3.org/1999/xhtml"><body><p>page</p></body></html>')->mimeType($declared))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
})->with([
    'text/*' => ['text/*', 'text/xml'],
    'application/*' => ['application/*', 'application/xml'],
]);

test('xml is still accepted when listed exactly', function () {
    Curator::acceptedFileTypes(['text/xml']);

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('note.xml', '<?xml version="1.0"?><note><to>x</to></note>')->mimeType('text/xml'))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Media::query()->sole()->type)->toBe('text/xml');
});

function detectedUploadTypeOle(): string
{
    // An OLE compound file header followed by more than 64 KiB, so sniffing the
    // first 64 KiB reports only the container.
    return "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" . str_repeat("\0", 70 * 1024);
}

test('legacy office files larger than the sniffed sample are accepted', function (string $name, string $type, string $ext) {
    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent($name, detectedUploadTypeOle())->mimeType($type))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->type)->toBe($type)
        ->and($media->ext)->toBe($ext);
})->with([
    'doc' => ['report.doc', 'application/msword', 'doc'],
    'xls' => ['sheet.xls', 'application/vnd.ms-excel', 'xls'],
    'ppt' => ['slides.ppt', 'application/vnd.ms-powerpoint', 'ppt'],
]);

test('an ole container is not treated as an office file without its signature or extension', function () {
    expect(MimeType::refineDetectedType('application/x-ole-storage', 'doc', fn (?int $length = null): string => str_repeat('A', $length ?? 100)))
        ->toBe('application/x-ole-storage')
        ->and(MimeType::refineDetectedType('application/x-ole-storage', 'bin', fn (?int $length = null): string => substr(detectedUploadTypeOle(), 0, $length)))
        ->toBe('application/x-ole-storage');
});

test('signature checks read only the first bytes', function (string $type, string $extension, string $contents, int $length) {
    $requested = [];

    MimeType::refineDetectedType($type, $extension, function (?int $length = null) use (&$requested, $contents): string {
        $requested[] = $length;

        return $length === null ? $contents : substr($contents, 0, $length);
    });

    expect($requested)->toBe([$length]);
})->with([
    'zip' => ['application/zip', 'docx', "PK\x03\x04rest", 4],
    'ole' => ['application/x-ole-storage', 'doc', "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1rest", 8],
]);

test('plain-data text detected only as text/plain gets its format type', function (string $name, string $type, string $ext) {
    // libmagic reports this content as text/plain on every platform, as some
    // builds do for any CSV.
    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent($name, "hello world\n")->mimeType('text/plain'))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->type)->toBe($type)
        ->and($media->ext)->toBe($ext);
})->with([
    'csv' => ['data.csv', 'text/csv', 'csv'],
    'tsv' => ['data.tsv', 'text/tab-separated-values', 'tsv'],
    'md' => ['notes.md', 'text/markdown', 'md'],
    'markdown' => ['notes.markdown', 'text/markdown', 'markdown'],
    'ics' => ['event.ics', 'text/calendar', 'ics'],
    'vtt' => ['captions.vtt', 'text/vtt', 'vtt'],
    'other text' => ['notes.txt', 'text/plain', 'txt'],
]);

test('a field accepting only csv accepts a csv detected as text/plain', function () {
    Curator::acceptedFileTypes(['text/csv']);

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('data.csv', "hello world\n")->mimeType('text/csv'))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Media::query()->sole()->type)->toBe('text/csv');
});

test('only text/plain content is refined, and only for plain-data extensions', function (string $type, string $extension, string $expected) {
    expect(MimeType::refineDetectedType($type, $extension, fn (?int $length = null): string => 'hello'))->toBe($expected);
})->with([
    ['text/plain', 'CSV', 'text/csv'],
    ['text/plain', 'html', 'text/plain'],
    ['text/plain', 'js', 'text/plain'],
    ['text/plain', 'xml', 'text/plain'],
    ['text/plain', 'svg', 'text/plain'],
    ['text/html', 'csv', 'text/html'],
    ['application/octet-stream', 'csv', 'application/octet-stream'],
]);
