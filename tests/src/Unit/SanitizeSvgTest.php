<?php

use function Awcodes\Curator\is_media_svg;
use function Awcodes\Curator\sanitize_svg;

test('is_media_svg detects the svg mime type', function () {
    expect(is_media_svg('image/svg+xml'))->toBeTrue()
        ->and(is_media_svg('image/png'))->toBeFalse()
        ->and(is_media_svg(null))->toBeFalse();
});

test('sanitize_svg strips scripts, event handlers and javascript hrefs', function () {
    $dirty = <<<'SVG'
    <svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)">
      <script>alert(document.cookie)</script>
      <a xlink:href="javascript:alert(1)"><text>x</text></a>
      <rect width="10" height="10" fill="green"/>
    </svg>
    SVG;

    $clean = sanitize_svg($dirty);

    expect($clean)
        ->not->toContain('<script')
        ->not->toContain('onload')
        ->not->toContain('javascript:')
        ->toContain('rect');
});

test('sanitize_svg fails closed on unparseable markup', function () {
    expect(sanitize_svg('<svg><rect'))->toBe('');
});
