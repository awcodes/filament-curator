<?php

declare(strict_types=1);

use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

function repairExtensionsJpeg(): string
{
    $image = imagecreatetruecolor(10, 10);
    ob_start();
    imagejpeg($image);
    imagedestroy($image);

    return (string) ob_get_clean();
}

function repairExtensionsZippedDocx(): string
{
    $path = tempnam(sys_get_temp_dir(), 'curator-docx');
    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::OVERWRITE);
    $zip->addFromString('docProps/app.xml', '<Properties/>');
    $zip->addFromString('[Content_Types].xml', '<Types/>');
    $zip->close();

    $content = (string) file_get_contents($path);
    @unlink($path);

    return $content;
}

test('dry run reports a mismatched dangerous extension without changing anything', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/poly.html', repairExtensionsJpeg() . '<script>alert(1)</script>');

    $media = makeMedia(['directory' => 'media', 'name' => 'poly', 'path' => 'media/poly.html', 'ext' => 'html']);

    $this->artisan('curator:repair-extensions --dry-run')
        ->expectsOutputToContain('would rename: [' . $media->id . '] media/poly.html -> media/poly.jpg')
        ->assertSuccessful();

    expect(Storage::disk('public')->exists('media/poly.html'))->toBeTrue()
        ->and(Storage::disk('public')->exists('media/poly.jpg'))->toBeFalse()
        ->and($media->refresh()->path)->toBe('media/poly.html')
        ->and($media->ext)->toBe('html');
});

test('renames a mismatched dangerous extension to the detected type', function () {
    Storage::fake('public');
    $content = repairExtensionsJpeg() . '<script>alert(1)</script>';
    Storage::disk('public')->put('media/poly.html', $content);

    $media = makeMedia(['directory' => 'media', 'name' => 'poly', 'path' => 'media/poly.html', 'ext' => 'html', 'type' => 'text/html']);

    $this->artisan('curator:repair-extensions')->assertSuccessful();

    $media->refresh();

    expect($media->path)->toBe('media/poly.jpg')
        ->and($media->ext)->toBe('jpg')
        ->and($media->type)->toBe('image/jpeg')
        ->and($media->url)->toEndWith('media/poly.jpg')
        ->and(Storage::disk('public')->exists('media/poly.html'))->toBeFalse()
        ->and(Storage::disk('public')->get('media/poly.jpg'))->toBe($content);
});

test('renames an extension carrying script', function () {
    Storage::fake('public');
    Storage::disk('public')->put("photo.png');alert(1);('", repairExtensionsJpeg());

    $media = makeMedia(['name' => 'photo', 'path' => "photo.png');alert(1);('", 'ext' => "png');alert(1);('"]);

    $this->artisan('curator:repair-extensions')->assertSuccessful();

    expect($media->refresh()->path)->toBe('photo.jpg')
        ->and($media->ext)->toBe('jpg');
});

test('sanitizes svg markup when it is renamed to svg', function () {
    Storage::fake('public');
    Storage::disk('public')->put('logo.html', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><rect width="1" height="1"/></svg>');

    $media = makeMedia(['name' => 'logo', 'path' => 'logo.html', 'ext' => 'html']);

    $this->artisan('curator:repair-extensions')->assertSuccessful();

    expect($media->refresh()->path)->toBe('logo.svg')
        ->and(Storage::disk('public')->exists('logo.html'))->toBeFalse()
        ->and(Storage::disk('public')->get('logo.svg'))->not->toContain('<script')->toContain('rect');
});

test('leaves consistent and harmless mismatched media alone', function () {
    Storage::fake('public');
    Storage::disk('public')->put('photo.jpeg', repairExtensionsJpeg());
    Storage::disk('public')->put('other.png', repairExtensionsJpeg());

    $consistent = makeMedia(['name' => 'photo', 'path' => 'photo.jpeg', 'ext' => 'jpeg']);
    $harmless = makeMedia(['name' => 'other', 'path' => 'other.png', 'ext' => 'png']);

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('mismatch, left as is: [' . $harmless->id . ']')
        ->assertSuccessful();

    expect($consistent->refresh()->path)->toBe('photo.jpeg')
        ->and($harmless->refresh()->path)->toBe('other.png')
        ->and(Storage::disk('public')->exists('other.png'))->toBeTrue();
});

test('skips a row when the target name is already taken', function () {
    Storage::fake('public');
    Storage::disk('public')->put('poly.html', repairExtensionsJpeg());
    Storage::disk('public')->put('poly.jpg', 'someone else');

    $media = makeMedia(['name' => 'poly', 'path' => 'poly.html', 'ext' => 'html']);

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('skipped (target exists)')
        ->assertSuccessful();

    expect($media->refresh()->path)->toBe('poly.html')
        ->and(Storage::disk('public')->get('poly.jpg'))->toBe('someone else');
});

test('skips records whose file is missing', function () {
    Storage::fake('public');

    makeMedia(['name' => 'gone', 'path' => 'gone.html', 'ext' => 'html']);

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('skipped (missing file)')
        ->assertSuccessful();
});

test('the live run reports the path a file was renamed from', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/poly.html', repairExtensionsJpeg());

    $media = makeMedia(['directory' => 'media', 'name' => 'poly', 'path' => 'media/poly.html', 'ext' => 'html']);

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('renamed: [' . $media->id . '] media/poly.html -> media/poly.jpg')
        ->assertSuccessful();
});

test('leaves healthy files with uppercase extensions in place', function (string $path) {
    Storage::fake('public');
    Storage::disk('public')->put($path, repairExtensionsJpeg());

    $media = makeMedia(['name' => 'photo', 'path' => $path, 'ext' => pathinfo($path, PATHINFO_EXTENSION)]);

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('mismatch (case only), left as is: [' . $media->id . ']')
        ->doesntExpectOutputToContain('renamed:')
        ->doesntExpectOutputToContain('skipped (')
        ->assertSuccessful();

    expect($media->refresh()->path)->toBe($path)
        ->and(Storage::disk('public')->exists($path))->toBeTrue();
})->with(['photo.JPG', 'photo.JPEG']);

test('renames an uppercase dangerous extension', function () {
    Storage::fake('public');
    Storage::disk('public')->put('poly.HTML', repairExtensionsJpeg());

    $media = makeMedia(['name' => 'poly', 'path' => 'poly.HTML', 'ext' => 'HTML']);

    $this->artisan('curator:repair-extensions')->assertSuccessful();

    expect($media->refresh()->path)->toBe('poly.jpg');
});

test('builds the new path from the stored file, not the stored name', function () {
    Storage::fake('public');
    Storage::disk('public')->put('media/poly.html', repairExtensionsJpeg());

    $media = makeMedia(['directory' => 'media', 'name' => '../other/victim', 'path' => 'media/poly.html', 'ext' => 'html']);

    $this->artisan('curator:repair-extensions')->assertSuccessful();

    expect($media->refresh()->path)->toBe('media/poly.jpg')
        ->and(Storage::disk('public')->allFiles())->toBe(['media/poly.jpg']);
});

test('renames html content to txt when the app does not accept html', function () {
    Storage::fake('public');
    $content = '<!DOCTYPE html><html><body><script>alert(1)</script></body></html>';
    Storage::disk('public')->put('page.html', $content);

    $media = makeMedia(['name' => 'page', 'path' => 'page.html', 'ext' => 'html', 'type' => 'text/html']);

    $this->artisan('curator:repair-extensions --dry-run')
        ->expectsOutputToContain('would rename: [' . $media->id . '] page.html -> page.txt')
        ->assertSuccessful();

    expect(Storage::disk('public')->exists('page.html'))->toBeTrue();

    $this->artisan('curator:repair-extensions')->assertSuccessful();

    expect($media->refresh()->path)->toBe('page.txt')
        ->and($media->ext)->toBe('txt')
        ->and($media->type)->toBe('text/plain')
        ->and(Storage::disk('public')->get('page.txt'))->toBe($content);

    // A second run recognises the neutralised file.
    $this->artisan('curator:repair-extensions')
        ->doesntExpectOutputToContain('[' . $media->id . ']')
        ->assertSuccessful();
});

test('only flags html content when the app accepts html', function () {
    Storage::fake('public');
    Curator::acceptedFileTypes([...MimeType::defaults(), 'text/html']);
    Storage::disk('public')->put('page.html', '<!DOCTYPE html><html><body><p>hi</p></body></html>');

    $media = makeMedia(['name' => 'page', 'path' => 'page.html', 'ext' => 'html', 'type' => 'text/html']);

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('review (holds text/html, which this app accepts): [' . $media->id . '] page.html')
        ->assertSuccessful();

    expect($media->refresh()->path)->toBe('page.html');
});

test('leaves healthy svg and office files alone', function () {
    Storage::fake('public');
    Storage::disk('public')->put('icon.svg', "<!-- icon -->\n<svg xmlns=\"http://www.w3.org/2000/svg\"><rect/></svg>");
    Storage::disk('public')->put('report.docx', repairExtensionsZippedDocx());

    $svg = makeMedia(['name' => 'icon', 'path' => 'icon.svg', 'ext' => 'svg', 'type' => 'image/svg+xml']);
    $docx = makeMedia(['name' => 'report', 'path' => 'report.docx', 'ext' => 'docx']);

    $this->artisan('curator:repair-extensions')
        ->doesntExpectOutputToContain('[' . $svg->id . ']')
        ->doesntExpectOutputToContain('[' . $docx->id . ']')
        ->assertSuccessful();

    expect($svg->refresh()->path)->toBe('icon.svg')
        ->and($docx->refresh()->path)->toBe('report.docx');
});

test('keeps the original when the sanitized svg cannot be written', function () {
    Storage::fake('public');
    Storage::disk('public')->put('logo.html', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><rect width="1" height="1"/></svg>');

    $fake = Storage::disk('public');

    Storage::set('public', new class($fake->getDriver(), $fake->getAdapter(), ['throw' => false]) extends FilesystemAdapter
    {
        public function put($path, $contents, $options = [])
        {
            return str_ends_with((string) $path, '.svg') ? false : parent::put($path, $contents, $options);
        }
    });

    $media = makeMedia(['name' => 'logo', 'path' => 'logo.html', 'ext' => 'html']);

    $this->artisan('curator:repair-extensions')
        ->expectsOutputToContain('could not write logo.svg')
        ->assertSuccessful();

    expect($media->refresh()->path)->toBe('logo.html')
        ->and(Storage::disk('public')->exists('logo.html'))->toBeTrue()
        ->and(Storage::disk('public')->exists('logo.svg'))->toBeFalse();
});
