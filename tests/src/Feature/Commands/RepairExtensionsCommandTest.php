<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

function repairExtensionsJpeg(): string
{
    $image = imagecreatetruecolor(10, 10);
    ob_start();
    imagejpeg($image);
    imagedestroy($image);

    return (string) ob_get_clean();
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
