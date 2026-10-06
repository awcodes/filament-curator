<?php

declare(strict_types=1);

use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GliderFallback;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

test('a density srcset keeps the requested size and asks Glide for the pixel ratio', function () {
    $media = makeMedia(['width' => 1600, 'height' => 900]);

    $html = Blade::render('<x-curator-glider :media="$media" width="400" :srcset="[\'1x\', \'2x\']" sizes="400px" />', ['media' => $media]);

    preg_match('/srcset="([^"]+)"/', html_entity_decode($html), $matches);

    $candidates = collect(explode(', ', $matches[1]))
        ->mapWithKeys(fn (string $candidate): array => [Str::afterLast($candidate, ' ') => Str::beforeLast($candidate, ' ')]);

    expect($candidates['1x'])->toContain('w=400')->toContain('dpr=1')
        ->and($candidates['2x'])->toContain('w=400')->toContain('dpr=2')
        ->and($matches[1])->not->toContain('w=2&');
});

test('a width srcset still requests each width', function () {
    $media = makeMedia(['width' => 1600, 'height' => 900]);

    $html = html_entity_decode(Blade::render('<x-curator-glider :media="$media" :srcset="[\'640w\']" sizes="100vw" />', ['media' => $media]));

    expect($html)->toMatch('/w=640[^ ]* 640w/');
});

test('a fallback source in the public directory is linked as an asset, not through Glide', function () {
    Glide::registerGliderFallbacks([
        GliderFallback::make('thumbnail')->source('/images/placeholder.jpg')->width(200)->height(200),
    ]);

    $html = Blade::render('<x-curator-glider :media="999" fallback="thumbnail" width="200" />');

    expect($html)
        ->toContain('src="' . asset('/images/placeholder.jpg') . '"')
        ->not->toContain(Glide::getBasePath());
});

test('a fallback source that is a stored media path still goes through Glide', function () {
    makeMedia(['path' => 'fallbacks/placeholder.jpg']);

    Glide::registerGliderFallbacks([
        GliderFallback::make('thumbnail')->source('fallbacks/placeholder.jpg')->type('jpg'),
    ]);

    expect(Blade::render('<x-curator-glider :media="999" fallback="thumbnail" width="200" />'))
        ->toContain(Glide::getBasePath() . '/fallbacks/placeholder.jpg');
});
