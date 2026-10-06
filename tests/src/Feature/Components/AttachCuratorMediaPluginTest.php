<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

// The editor inserts media as a plain image. Filament reads an image's data-id as a file attachment path and
// replaces its src when rendering, which is why the media key is no longer stored there.

test('inserted media keeps its src when rendered', function () {
    $html = RichContentRenderer::make('<p><img src="/storage/mountain-dusk.jpg" alt="Dusk" title="Mountain dusk"></p>')
        ->plugins([AttachCuratorMediaPlugin::make()])
        ->toHtml();

    expect($html)->toContain('src="/storage/mountain-dusk.jpg"');
});

test('an image that still carries a media key in data-id loses its src', function () {
    $html = RichContentRenderer::make('<p><img src="/storage/mountain-dusk.jpg" alt="Dusk" data-id="42"></p>')
        ->plugins([AttachCuratorMediaPlugin::make()])
        ->toHtml();

    expect($html)->not->toContain('src="/storage/mountain-dusk.jpg"');
});

test('the integration script no longer sends the media key as the image id', function () {
    $script = file_get_contents(__DIR__ . '/../../../../resources/dist/rich-editor-integration.js');

    expect($script)->toContain('setImage')
        ->and($script)->not->toMatch('/title:[^,}]+,id:/');
});
