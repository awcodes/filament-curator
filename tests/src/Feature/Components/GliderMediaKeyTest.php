<?php

declare(strict_types=1);

use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GliderFallback;
use Awcodes\Curator\View\Components\Glider;
use Illuminate\Support\Facades\Blade;

// A media id arrives as a string whenever it comes back out of a json column, a
// settings array or a query string. It used to be taken for a path, which left
// the component rendering the document placeholder for a perfectly good image.
// See https://github.com/awcodes/filament-curator/issues/719.

test('a numeric string id resolves the record rather than being read as a path', function () {
    $media = makeMedia(['path' => 'real.jpg', 'ext' => 'jpg']);

    $glider = new Glider(media: (string) $media->id);

    expect($glider->mediaItem->getPath())->toBe('real.jpg')
        ->and($glider->mediaItem->isPreviewable())->toBeTrue();
});

test('a path is still treated as a path', function () {
    $glider = new Glider(media: 'images/banner.jpg');

    expect($glider->mediaItem->getPath())->toBe('images/banner.jpg')
        ->and($glider->mediaItem->isPreviewable())->toBeTrue();
});

test('a string id that does not resolve reaches the fallback', function () {
    Glide::registerGliderFallbacks([
        GliderFallback::make('thumbnail')->source('fallback.jpg')->type('jpg'),
    ]);

    $glider = new Glider(media: '999', fallback: 'thumbnail');

    expect($glider->mediaItem->getPath())->toBe('fallback.jpg');
});

// The install command offers uuid primary keys, so a key is not always numeric.
test('isMediaKey recognises the key shapes curator issues', function (string $value) {
    expect(Glider::isMediaKey($value))->toBeTrue();
})->with([
    '10',
    '9223372036854775807',
    '3f2504e0-4f89-11d3-9a0c-0305e82c3301',
    '01ARZ3NDEKTSV4RRFFQ69G5FAV',
]);

test('isMediaKey leaves anything path shaped alone', function (string $value) {
    expect(Glider::isMediaKey($value))->toBeFalse();
})->with([
    'images/banner.jpg',
    'banner.jpg',
    '10.jpg',
    'media/2024/10',
    '',
]);

// The document placeholder defaulted its label to "pdf" and the glider never
// passed the real one, so every non-previewable file rendered as a PDF.
test('a record media item carries its extension to the placeholder', function () {
    $media = makeMedia(['path' => 'notes.docx', 'ext' => 'docx', 'type' => 'application/msword']);

    $glider = new Glider(media: $media->id);

    expect($glider->mediaItem->isPreviewable())->toBeFalse()
        ->and($glider->mediaItem->getExtension())->toBe('docx');
});

test('a path media item carries its extension too', function () {
    $glider = new Glider(media: 'archive/report.docx');

    expect($glider->mediaItem->getExtension())->toBe('docx');
});

test('the rendered placeholder labels the real extension instead of pdf', function () {
    $media = makeMedia(['path' => 'notes.docx', 'ext' => 'docx', 'type' => 'application/msword']);

    $html = Blade::render('<x-curator-glider :media="$media" />', ['media' => $media->id]);

    expect($html)->toContain('>docx<')
        ->and($html)->not->toContain('>pdf<');
});

test('a fallback media item carries its extension too', function () {
    Glide::registerGliderFallbacks([
        GliderFallback::make('doc')->source('placeholder.docx'),
    ]);

    $glider = new Glider(media: null, fallback: 'doc');

    expect($glider->mediaItem->getExtension())->toBe('docx');
});
