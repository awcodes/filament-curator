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
 * Livewire detects an upload's type from its bytes in production, but fakes
 * report the type declared on them, so each upload declares what the bytes
 * would be detected as.
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

function trustedExtensionUpload(string $name, string $content, string $type): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, $content)->mimeType($type);
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
        ->set('data.file', trustedExtensionUpload('poly.html', trustedExtensionJpeg() . '<script>alert(1)</script>', 'image/jpeg'))
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
        ->set('data.file', trustedExtensionUpload("photo.png');alert(1);('", trustedExtensionJpeg(), 'image/png'))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('png')
        ->and($media->path)->toMatch('/^[a-z0-9\/-]+\.png$/');
});

test('the extension is replaced even when filenames are preserved', function () {
    Storage::fake('public');
    app(CuratorManager::class)->preserveFilenames(true);

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload('Holiday Photo.html', trustedExtensionJpeg(), 'image/jpeg'))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Media::query()->sole()->path)->toEndWith('holiday-photo.jpg');
});

test('ordinary uploads keep their extension', function (string $name, string $content, string $type, string $ext) {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', trustedExtensionUpload($name, $content, $type))
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
    'csv detected as plain text' => ['data.csv', "a,b\n1,2\n", 'text/plain', 'csv'],
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
        ->set('data.file', trustedExtensionUpload('logo.html', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><rect width="1" height="1"/></svg>', 'image/svg+xml'))
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
