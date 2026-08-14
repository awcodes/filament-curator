<?php

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;

const SVG_SCRIPT_PAYLOAD = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(document.domain)</script></svg>';

function servedMedia(string $path, string $ext, string $type, string $contents): Media
{
    Storage::fake('public');

    $media = Media::factory()->create([
        'path' => $path,
        'ext' => $ext,
        'type' => $type,
    ]);

    Storage::disk($media->disk)->put($path, $contents);

    return $media;
}

test('svg markup stored under another extension is not served as svg', function () {
    // Flysystem types streamed files by sniffing their bytes, so before the
    // content type was pinned this came back as image/svg+xml and rendered as
    // a document in the panel's own origin.
    $media = servedMedia('payload.txt', 'txt', 'image/svg+xml', SVG_SCRIPT_PAYLOAD);

    $response = $this->get($media->getSignedUrl(force: true));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('text/plain')
        ->and($response->headers->get('X-Content-Type-Options'))->toBe('nosniff');
});

test('svg markup stored under an unmapped extension is forced to download', function () {
    $media = servedMedia('payload.q7z', 'q7z', 'image/svg+xml', SVG_SCRIPT_PAYLOAD);

    $response = $this->get($media->getSignedUrl(force: true));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('application/octet-stream')
        ->and($response->headers->get('Content-Disposition'))->toStartWith('attachment');
});

test('a genuine svg is still served inline as svg', function () {
    $media = servedMedia('logo.svg', 'svg', 'image/svg+xml', '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

    $response = $this->get($media->getSignedUrl(force: true));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('image/svg+xml')
        ->and($response->headers->get('Content-Disposition'))->toStartWith('inline');
});

test('a document is still served inline with its declared type', function () {
    $media = servedMedia('report.pdf', 'pdf', 'application/pdf', '%PDF-1.4 fake');

    $response = $this->get($media->getSignedUrl(force: true));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('application/pdf')
        ->and($response->headers->get('Content-Disposition'))->toStartWith('inline');
});
