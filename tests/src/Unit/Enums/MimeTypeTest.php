<?php

declare(strict_types=1);

use Awcodes\Curator\Enums\MimeType;

test('toArray returns an array of strings', function () {
    $types = MimeType::toArray();

    expect($types)->toBeArray()
        ->and($types)->not->toBeEmpty()
        ->and($types[0])->toBeString();
});

test('every value contains a forward slash', function () {
    $types = MimeType::toArray();

    foreach ($types as $type) {
        expect($type)->toContain('/');
    }
});

test('common image types are present', function () {
    $types = MimeType::toArray();

    expect($types)->toContain('image/jpeg')
        ->and($types)->toContain('image/png');
});

test('application/pdf is present', function () {
    $types = MimeType::toArray();

    expect($types)->toContain('application/pdf');
});

test('video/mp4 is present', function () {
    $types = MimeType::toArray();

    expect($types)->toContain('video/mp4');
});

test('defaults exclude every executable type', function (string $type) {
    expect(MimeType::defaults())->not->toContain($type)
        ->and(MimeType::toArray())->toContain($type);
})->with([
    'text/html',
    'application/xhtml+xml',
    'text/javascript',
    'application/xml',
    'application/x-httpd-php',
    'application/x-sh',
    'application/x-csh',
    'application/x-shockwave-flash',
    'application/vnd.mozilla.xul+xml',
    'application/octet-stream',
]);

test('defaults still contain the media types the package exists to serve', function () {
    expect(MimeType::defaults())
        ->toContain('image/jpeg')
        ->toContain('image/png')
        ->toContain('image/webp')
        ->toContain('image/svg+xml')
        ->toContain('application/pdf')
        ->toContain('video/mp4')
        ->toContain('audio/mpeg')
        ->toContain('text/plain');
});

test('defaults are exactly toArray minus restricted', function () {
    expect(MimeType::defaults())
        ->toHaveCount(count(MimeType::toArray()) - count(MimeType::restricted()));
});

test('restrictedExtensions maps the restricted types to extensions', function () {
    expect(MimeType::restrictedExtensions())
        ->toContain('html')
        ->toContain('xhtml')
        ->toContain('js')
        ->toContain('xml')
        ->toContain('php')
        ->toContain('sh')
        ->toContain('csh')
        ->toContain('svf')
        ->toContain('xul')
        ->toContain('bin')
        ->not->toContain('jpg')
        ->not->toContain('svg')
        ->not->toContain('pdf');
});

test('tryFromExtension resolves the type an extension declares', function () {
    expect(MimeType::tryFromExtension('svg'))->toBe(MimeType::ImageSvgXml)
        ->and(MimeType::tryFromExtension('SVG'))->toBe(MimeType::ImageSvgXml)
        ->and(MimeType::tryFromExtension('txt'))->toBe(MimeType::TextPlain)
        ->and(MimeType::tryFromExtension('pdf'))->toBe(MimeType::ApplicationPdf)
        ->and(MimeType::tryFromExtension('html'))->toBe(MimeType::TextHtml);
});

test('tryFromExtension returns null for extensions outside the enum', function () {
    expect(MimeType::tryFromExtension('dat'))->toBeNull()
        ->and(MimeType::tryFromExtension('ogg'))->toBeNull()
        ->and(MimeType::tryFromExtension(''))->toBeNull()
        ->and(MimeType::tryFromExtension(null))->toBeNull();
});
