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
