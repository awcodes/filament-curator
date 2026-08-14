<?php

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;

$dirtySvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)">
  <script>alert(document.cookie)</script>
  <rect width="10" height="10" fill="green"/>
</svg>
SVG;

test('sanitizes stored svg files in place', function () use ($dirtySvg) {
    Storage::fake('public');

    $media = Media::factory()->type('svg')->create()->fresh();
    Storage::disk($media->disk)->put($media->path, $dirtySvg);

    $this->artisan('curator:sanitize-svgs')->assertSuccessful();

    $stored = Storage::disk($media->disk)->get($media->path);

    expect($stored)
        ->not->toContain('<script')
        ->not->toContain('onload')
        ->toContain('rect');

    expect($media->fresh()->size)->toBe(Storage::disk($media->disk)->size($media->path));
});

test('dry run reports changes without writing', function () use ($dirtySvg) {
    Storage::fake('public');

    $media = Media::factory()->type('svg')->create()->fresh();
    Storage::disk($media->disk)->put($media->path, $dirtySvg);

    $this->artisan('curator:sanitize-svgs --dry-run')->assertSuccessful();

    expect(Storage::disk($media->disk)->get($media->path))->toContain('<script');
});

test('reports when there are no svg files', function () {
    Storage::fake('public');

    Media::factory()->create(); // a non-svg image

    $this->artisan('curator:sanitize-svgs')
        ->expectsOutputToContain('No SVG media found.')
        ->assertSuccessful();
});

test('scanning more rows than one chunk terminates', function () use ($dirtySvg) {
    Storage::fake('public');

    // Rows match on both type and ext. With the or ungrouped, chunkById's
    // `and id > ?` binds to the ext test alone, so every type-matched row comes
    // back on each pass and the scan never advances past the first chunk.
    //
    // count() returns a fresh factory instance and would drop the svg state,
    // so the rows are created one at a time.
    foreach (range(1, 120) as $ignored) {
        $media = Media::factory()->type('svg')->create();
        Storage::disk($media->disk)->put($media->path, $dirtySvg);
    }

    $this->artisan('curator:sanitize-svgs')->assertSuccessful();

    $dirty = Media::query()->get()->filter(
        fn (Media $media) => str_contains(Storage::disk($media->disk)->get($media->path), '<script'),
    );

    expect($dirty)->toHaveCount(0);
});
