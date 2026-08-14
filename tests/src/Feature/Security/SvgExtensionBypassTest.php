<?php

declare(strict_types=1);

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\CuratorUtils;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

const SVG_SCRIPT_PAYLOAD = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(document.domain)</script></svg>';

/**
 * Builds an upload the way a browser would: the bytes decide the detected type,
 * the filename is whatever the client claimed.
 *
 * The mime has to be declared explicitly because fakes report the type their
 * *extension* implies, whereas Livewire detects it from the stored bytes in
 * production (TemporaryUploadedFile::getMimeType() only reads fake metadata
 * under runningUnitTests). libmagic reports image/svg+xml for this markup under
 * any filename, so declaring it here matches what a real upload would detect.
 */
function svgUploadNamed(string $name): UploadedFile
{
    return UploadedFile::fake()
        ->createWithContent($name, SVG_SCRIPT_PAYLOAD)
        ->mimeType('image/svg+xml');
}

test('svg markup uploaded under a non-svg extension is still sanitized', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', svgUploadNamed('payload.txt'))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->first();

    expect($media)->not->toBeNull()
        ->and(Storage::disk('public')->get($media->path))->not->toContain('<script>');
});

test('svg markup uploaded with no extension at all is still sanitized', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', svgUploadNamed('payload'))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->first();

    expect($media)->not->toBeNull()
        ->and(Storage::disk('public')->get($media->path))->not->toContain('<script>');
});

test('an honestly named svg is still sanitized', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', svgUploadNamed('logo.svg'))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->first();

    expect($media->ext)->toBe('svg')
        ->and(Storage::disk('public')->get($media->path))->not->toContain('<script>');
});

test('imported svg media is sanitized regardless of the source extension', function () {
    Storage::fake('public');

    $source = tempnam(sys_get_temp_dir(), 'curator-import');
    file_put_contents($source, SVG_SCRIPT_PAYLOAD);

    $data = CuratorUtils::importMedia($source, disk: 'public');

    expect(Storage::disk('public')->get($data['path']))->not->toContain('<script>');
});

test('a pre-patch row storing svg under another extension is not served as svg', function () {
    Storage::fake('public');
    Storage::disk('public')->put('payload.txt', SVG_SCRIPT_PAYLOAD);

    $media = makeMedia([
        'name' => 'payload',
        'path' => 'payload.txt',
        'type' => 'image/svg+xml',
        'ext' => 'txt',
    ]);

    $response = $this->get(app(GlideManager::class)->getUrl($media->path));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('text/plain')
        ->and($response->headers->get('X-Content-Type-Options'))->toBe('nosniff');
});

test('svg stored under an extension curator does not know is forced to download', function () {
    Storage::fake('public');
    Storage::disk('public')->put('payload.dat', SVG_SCRIPT_PAYLOAD);

    $media = makeMedia([
        'name' => 'payload',
        'path' => 'payload.dat',
        'type' => 'image/svg+xml',
        'ext' => 'dat',
    ]);

    $response = $this->get(app(GlideManager::class)->getUrl($media->path));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('application/octet-stream')
        ->and($response->headers->get('Content-Disposition'))->toStartWith('attachment');
});

test('a genuine svg is still served inline as svg', function () {
    Storage::fake('public');
    Storage::disk('public')->put('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

    $media = makeMedia([
        'name' => 'logo',
        'path' => 'logo.svg',
        'type' => 'image/svg+xml',
        'ext' => 'svg',
    ]);

    $response = $this->get(app(GlideManager::class)->getUrl($media->path));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('image/svg+xml')
        ->and($response->headers->get('Content-Disposition'))->toStartWith('inline');
});

test('media with an extension outside the enum is still served with its sniffed type', function () {
    Storage::fake('public');
    Storage::disk('public')->put('clip.ogg', 'OggS fake audio');

    $media = makeMedia([
        'name' => 'clip',
        'path' => 'clip.ogg',
        'type' => 'audio/ogg',
        'ext' => 'ogg',
    ]);

    $response = $this->get(app(GlideManager::class)->getUrl($media->path));

    $response->assertOk();
    expect($response->headers->get('Content-Disposition'))->toStartWith('inline');
});
