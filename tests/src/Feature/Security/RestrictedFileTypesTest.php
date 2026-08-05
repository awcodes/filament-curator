<?php

declare(strict_types=1);

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

const XSS_PAYLOAD = '<!DOCTYPE html><html><body><script>alert(document.domain)</script></body></html>';

test('an html file is rejected by the default accepted file types', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('evil.html', XSS_PAYLOAD))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0);
});

// Most magic databases report php source as `text/x-php`, which was never in the
// enum, so this already passed before the defaults were tightened. It guards the
// systems whose finfo reports `application/x-httpd-php` instead, where the old
// defaults would have accepted an executable file into a web-served directory.
test('a php file is rejected by the default accepted file types', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->createWithContent('shell.php', '<?php echo shell_exec($_GET["c"]); ?>'))
        ->call('create')
        ->assertHasFormErrors(['file']);

    expect(Media::query()->count())->toBe(0);
});

test('legitimate images still upload with the tightened defaults', function () {
    Storage::fake('public');

    Livewire::test(CreateMedia::class)
        ->set('data.file', UploadedFile::fake()->image('photo.jpg', 100, 100))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Media::query()->count())->toBe(1);
});

test('a restricted file already on disk is served as an attachment', function () {
    Storage::fake('public');
    Storage::disk('public')->put('legacy.html', XSS_PAYLOAD);

    $media = makeMedia([
        'name' => 'legacy',
        'path' => 'legacy.html',
        'type' => 'text/html',
        'ext' => 'html',
    ]);

    $response = $this->get(app(GlideManager::class)->getUrl($media->path));

    $response->assertOk();
    expect($response->headers->get('Content-Disposition'))->toStartWith('attachment')
        ->and($response->headers->get('X-Content-Type-Options'))->toBe('nosniff');
});

test('a normal document is still served inline with nosniff', function () {
    Storage::fake('public');
    Storage::disk('public')->put('report.pdf', '%PDF-1.4 fake');

    $media = makeMedia([
        'name' => 'report',
        'path' => 'report.pdf',
        'type' => 'application/pdf',
        'ext' => 'pdf',
    ]);

    $response = $this->get(app(GlideManager::class)->getUrl($media->path));

    $response->assertOk();
    expect($response->headers->get('Content-Disposition'))->toStartWith('inline')
        ->and($response->headers->get('X-Content-Type-Options'))->toBe('nosniff');
});
