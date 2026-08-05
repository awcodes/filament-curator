<?php

declare(strict_types=1);

use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Enums\MimeType;

test('getDiskName returns config default', function () {
    $manager = new CuratorManager();

    expect($manager->getDiskName())->toBe(config('curator.default_disk'));
});

test('getDiskName returns explicit value', function () {
    $manager = new CuratorManager();
    $manager->disk('s3');

    expect($manager->getDiskName())->toBe('s3');
});

test('getDiskName evaluates closure', function () {
    $manager = new CuratorManager();
    $manager->disk(fn () => 'from-closure');

    expect($manager->getDiskName())->toBe('from-closure');
});

test('getDirectory returns null by default', function () {
    $manager = new CuratorManager();

    expect($manager->getDirectory())->toBeNull();
});

test('getDirectory returns explicit value', function () {
    $manager = new CuratorManager();
    $manager->directory('uploads');

    expect($manager->getDirectory())->toBe('uploads');
});

test('getVisibility returns public by default', function () {
    $manager = new CuratorManager();

    expect($manager->getVisibility())->toBe('public');
});

test('getVisibility returns explicit value', function () {
    $manager = new CuratorManager();
    $manager->visibility('private');

    expect($manager->getVisibility())->toBe('private');
});

test('getVisibility evaluates closure', function () {
    $manager = new CuratorManager();
    $manager->visibility(fn () => 'private');

    expect($manager->getVisibility())->toBe('private');
});

test('getAcceptedFileTypes returns the safe MimeType defaults', function () {
    $manager = new CuratorManager();

    expect($manager->getAcceptedFileTypes())->toBe(MimeType::defaults())
        ->and($manager->getAcceptedFileTypes())->not->toContain('text/html');
});

test('getAcceptedFileTypes returns custom types when set', function () {
    $manager = new CuratorManager();
    $manager->acceptedFileTypes(['image/jpeg', 'image/png']);

    expect($manager->getAcceptedFileTypes())->toBe(['image/jpeg', 'image/png']);
});

test('getMaxSize returns 5000 by default', function () {
    $manager = new CuratorManager();

    expect($manager->getMaxSize())->toBe(5000);
});

test('getMaxSize returns set value', function () {
    $manager = new CuratorManager();
    $manager->maxSize(10240);

    expect($manager->getMaxSize())->toBe(10240);
});

test('getMinSize returns 0 by default', function () {
    $manager = new CuratorManager();

    expect($manager->getMinSize())->toBe(0);
});

test('shouldPreserveFilenames returns false by default', function () {
    $manager = new CuratorManager();

    expect($manager->shouldPreserveFilenames())->toBeFalse();
});

test('shouldPreserveFilenames returns true when set', function () {
    $manager = new CuratorManager();
    $manager->preserveFilenames(true);

    expect($manager->shouldPreserveFilenames())->toBeTrue();
});

test('renderable type checks are case-insensitive', function (string $extension) {
    $manager = new CuratorManager();

    expect($manager->isResizable($extension))->toBeTrue()
        ->and($manager->isPreviewable($extension))->toBeTrue()
        ->and($manager->isDocument($extension))->toBeFalse();
})->with(['JPG', 'JPEG', 'PNG', 'WebP', 'Bmp']);

test('isSvg and isAudio are case-insensitive', function () {
    $manager = new CuratorManager();

    expect($manager->isSvg('SVG'))->toBeTrue()
        ->and($manager->isAudio('MP3'))->toBeTrue();
});

test('sanitizeSvg strips scripts, event handlers and javascript hrefs', function () {
    $manager = new CuratorManager();

    $dirty = <<<'SVG'
    <svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)">
      <script>alert(document.cookie)</script>
      <a xlink:href="javascript:alert(1)"><text>x</text></a>
      <rect width="10" height="10" fill="green"/>
    </svg>
    SVG;

    $clean = $manager->sanitizeSvg($dirty);

    expect($clean)
        ->not->toContain('<script')
        ->not->toContain('onload')
        ->not->toContain('javascript:')
        ->toContain('rect');
});

test('sanitizeSvg fails closed on unparseable markup', function () {
    $manager = new CuratorManager();

    expect($manager->sanitizeSvg('<svg><rect'))->toBe('');
});
