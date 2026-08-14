<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

$dirtySvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)">
  <script>alert(document.cookie)</script>
  <rect width="10" height="10" fill="green"/>
</svg>
SVG;

test('sanitizes stored svg files in place', function () use ($dirtySvg) {
    Storage::fake('public');
    Storage::disk('public')->put('dirty.svg', $dirtySvg);

    $media = makeMedia(['path' => 'dirty.svg', 'ext' => 'svg', 'type' => 'image/svg+xml']);

    $this->artisan('curator:sanitize-svgs')->assertSuccessful();

    $stored = Storage::disk('public')->get('dirty.svg');

    expect($stored)
        ->not->toContain('<script')
        ->not->toContain('onload')
        ->toContain('rect');

    // size column is refreshed to match the rewritten file
    expect($media->refresh()->size)->toBe(Storage::disk('public')->size('dirty.svg'));
});

test('dry run reports changes without writing', function () use ($dirtySvg) {
    Storage::fake('public');
    Storage::disk('public')->put('dirty.svg', $dirtySvg);

    makeMedia(['path' => 'dirty.svg', 'ext' => 'svg', 'type' => 'image/svg+xml']);

    $this->artisan('curator:sanitize-svgs --dry-run')->assertSuccessful();

    expect(Storage::disk('public')->get('dirty.svg'))->toContain('<script');
});

test('leaves non-svg media untouched', function () {
    Storage::fake('public');
    Storage::disk('public')->put('photo.jpg', 'not-an-svg');

    makeMedia(['path' => 'photo.jpg', 'ext' => 'jpg']);

    $this->artisan('curator:sanitize-svgs')
        ->expectsOutputToContain('No SVG media found.')
        ->assertSuccessful();

    expect(Storage::disk('public')->get('photo.jpg'))->toBe('not-an-svg');
});

test('skips records whose file is missing', function () {
    Storage::fake('public');

    makeMedia(['path' => 'gone.svg', 'ext' => 'svg', 'type' => 'image/svg+xml']);

    $this->artisan('curator:sanitize-svgs')
        ->expectsOutputToContain('skipped (missing file)')
        ->assertSuccessful();
});

test('sanitizes svg stored under a spoofed extension', function () use ($dirtySvg) {
    Storage::fake('public');
    Storage::disk('public')->put('payload.txt', $dirtySvg);

    // Uploaded before the sanitizer was gated on the detected type, so the row
    // carries the real type but an extension that hides it from an ext-only scan.
    makeMedia(['path' => 'payload.txt', 'ext' => 'txt', 'type' => 'image/svg+xml']);

    $this->artisan('curator:sanitize-svgs')->assertSuccessful();

    expect(Storage::disk('public')->get('payload.txt'))
        ->not->toContain('<script')
        ->not->toContain('onload');
});

test('chunking still scopes the id cursor to matching rows', function () use ($dirtySvg) {
    Storage::fake('public');

    // Enough rows to force more than one chunk, mixing both selection criteria
    // so a mis-grouped `or` would drag unrelated media into the scan.
    foreach (range(1, 120) as $i) {
        Storage::disk('public')->put("dirty-{$i}.svg", $dirtySvg);
        makeMedia(['name' => "dirty-{$i}", 'path' => "dirty-{$i}.svg", 'ext' => 'svg', 'type' => 'image/svg+xml']);
    }

    Storage::disk('public')->put('photo.jpg', 'not-an-svg');
    makeMedia(['name' => 'photo', 'path' => 'photo.jpg', 'ext' => 'jpg', 'type' => 'image/jpeg']);

    $this->artisan('curator:sanitize-svgs')->assertSuccessful();

    expect(Storage::disk('public')->get('dirty-120.svg'))->not->toContain('<script')
        ->and(Storage::disk('public')->get('photo.jpg'))->toBe('not-an-svg');
});
