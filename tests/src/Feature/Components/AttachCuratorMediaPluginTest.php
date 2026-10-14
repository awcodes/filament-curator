<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Awcodes\Curator\Components\Forms\RichEditor\CuratorFileAttachmentProvider;
use Awcodes\Curator\Components\Modals\CuratorPanel;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

// By default the editor inserts media as a plain image. Filament reads an image's data-id as a file attachment path
// and replaces its src when rendering, so the media key is stored there only when the plugin stores media ids and
// supplies a provider that can read it.

test('inserted media keeps its src when rendered', function () {
    $html = RichContentRenderer::make('<p><img src="/storage/mountain-dusk.jpg" alt="Dusk" title="Mountain dusk"></p>')
        ->plugins([AttachCuratorMediaPlugin::make()])
        ->toHtml();

    expect($html)->toContain('src="/storage/mountain-dusk.jpg"');
});

test('an image with a media key in data-id loses its src without the provider', function () {
    $html = RichContentRenderer::make('<p><img src="/storage/mountain-dusk.jpg" alt="Dusk" data-id="42"></p>')
        ->plugins([AttachCuratorMediaPlugin::make()])
        ->toHtml();

    expect($html)->not->toContain('src="/storage/mountain-dusk.jpg"');
});

test('the plugin supplies no provider unless it stores media ids', function () {
    expect(AttachCuratorMediaPlugin::make()->getFileAttachmentProvider())->toBeNull()
        ->and(AttachCuratorMediaPlugin::make()->storeMediaIds()->getFileAttachmentProvider())->toBeInstanceOf(CuratorFileAttachmentProvider::class)
        ->and(AttachCuratorMediaPlugin::make()->storeMediaIds(false)->getFileAttachmentProvider())->toBeNull();
});

test('the plugin supplies the provider it is given', function () {
    $provider = CuratorFileAttachmentProvider::make()->disk('local');

    expect(AttachCuratorMediaPlugin::make()->storeMediaIds(provider: $provider)->getFileAttachmentProvider())->toBe($provider);
});

test('an image with a media key renders from the library when the plugin stores media ids', function () {
    Storage::fake('public');

    $media = makeMedia(['path' => 'media/current.jpg']);

    $html = RichContentRenderer::make("<p><img src=\"/storage/stale.jpg\" alt=\"Dusk\" data-id=\"{$media->getKey()}\"></p>")
        ->plugins([AttachCuratorMediaPlugin::make()->storeMediaIds()])
        ->toHtml();

    expect($html)->toContain('src="' . Storage::disk('public')->url('media/current.jpg') . '"')
        ->and($html)->toContain('data-id="' . $media->getKey() . '"')
        ->and($html)->not->toContain('stale.jpg');
});

test('the panel tells the editor whether to store media ids', function (bool $storeMediaIds) {
    Storage::fake('public');

    $media = makeMedia();

    Livewire::test(CuratorPanel::class, ['settings' => [
        'diskName' => 'public',
        'acceptedFileTypes' => ['image/*'],
        'statePath' => 'data.content',
        'context' => 'richEditor',
        'storeMediaIds' => $storeMediaIds,
    ]])
        ->set('selected', [['id' => $media->getKey()]])
        ->callAction('insertMedia')
        ->assertDispatched('insert-media', fn (string $name, array $params): bool => ($params[0]['storeMediaIds'] ?? null) === $storeMediaIds
            && ($params[0]['media'][0]['id'] ?? null) === $media->getKey());
})->with([true, false]);

test('the integration script sends the media key as the image id only when told to', function () {
    $script = file_get_contents(__DIR__ . '/../../../../resources/dist/rich-editor-integration.js');

    expect($script)->toContain('setImage')
        ->and($script)->not->toMatch('/title:[^,}]+,id:/')
        ->and($script)->toMatch('/storeMediaIds&&\w+\.id\?\{id:\w+\.id\}/');
});
