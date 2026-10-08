<?php

declare(strict_types=1);

use Awcodes\Curator\CuratorUtils;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

/**
 * An XHTML document that contains an <svg> element is detected as SVG, but its
 * root isn't <svg>, so the sanitizer throws instead of returning markup.
 */
const UNSANITIZABLE_SVG = '<?xml version="1.0"?><html xmlns="http://www.w3.org/1999/xhtml"><body><svg/><script>alert(1)</script></body></html>';

beforeEach(function () {
    config(['curator.default_disk' => 'public']);
    Storage::fake('public');
});

test('the sanitizer fails closed when it throws', function () {
    expect(Curator::sanitizeSvg(UNSANITIZABLE_SVG))->toBe('');
});

test('an svg upload the sanitizer cannot handle is rejected and leaves no file', function (string $name) {
    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent($name, UNSANITIZABLE_SVG)->mimeType('image/svg+xml'))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
})->with(['page.svg', 'page.html']);

test('an imported svg the sanitizer cannot handle is not stored', function () {
    $directory = sys_get_temp_dir() . '/curator-unsanitizable-' . bin2hex(random_bytes(6));
    mkdir($directory);
    file_put_contents($directory . '/page.svg', UNSANITIZABLE_SVG);

    expect(fn () => CuratorUtils::importMedia($directory . '/page.svg', disk: 'public'))->toThrow(Exception::class)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

test('a valid svg upload is still sanitized and stored', function () {
    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script><rect width="1" height="1"/></svg>')->mimeType('image/svg+xml'))
        ->call('create')
        ->assertHasNoFormErrors();

    $media = Media::query()->sole();

    expect($media->ext)->toBe('svg')
        ->and(Storage::disk('public')->get($media->path))->not->toContain('<script')->toContain('rect')
        ->and($media->size)->toBe(Storage::disk('public')->size($media->path));
});
