<?php

declare(strict_types=1);

use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GliderFallback;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
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

test('a path given directly still goes through Glide, with or without a leading slash', function () {
    makeMedia(['path' => 'uploads/x.jpg']);

    expect(Blade::render('<x-curator-glider media="/uploads/x.jpg" width="200" />'))
        ->toContain(Glide::getBasePath() . '/uploads/x.jpg')
        ->and(Blade::render('<x-curator-glider media="images/not-a-record.jpg" width="200" />'))
        ->toContain(Glide::getBasePath() . '/images/not-a-record.jpg');
});

test('a fallback looks up whether it is stored media at most once per render', function () {
    Glide::registerGliderFallbacks([
        GliderFallback::make('thumbnail')->source('/images/placeholder.jpg')->width(200)->height(200),
    ]);

    DB::enableQueryLog();

    Blade::render('<x-curator-glider fallback="thumbnail" :srcset="[\'1x\', \'2x\', \'640w\']" sizes="100vw" />');

    $lookups = collect(DB::getQueryLog())->filter(fn (array $query): bool => str_contains($query['query'], '"path"'));

    expect($lookups)->toHaveCount(1);
});

test('a fallback stored with a leading slash difference still goes through Glide', function () {
    makeMedia(['path' => 'fallbacks/placeholder.jpg']);

    Glide::registerGliderFallbacks([
        GliderFallback::make('thumbnail')->source('/fallbacks/placeholder.jpg')->type('jpg'),
    ]);

    expect(Blade::render('<x-curator-glider fallback="thumbnail" width="200" />'))
        ->toContain(Glide::getBasePath() . '/');
});

test('a fallback matches stored media whose path has a leading slash', function () {
    makeMedia(['path' => '/fallbacks/legacy.jpg']);

    Glide::registerGliderFallbacks([
        GliderFallback::make('legacy')->source('fallbacks/legacy.jpg')->type('jpg'),
    ]);

    expect(Blade::render('<x-curator-glider fallback="legacy" width="200" />'))
        ->toContain(Glide::getBasePath() . '/');
});
