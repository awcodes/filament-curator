<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Awcodes\Curator\Components\Forms\RichEditor\CuratorFileAttachmentProvider;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Livewire\RichEditorForm;
use Awcodes\Curator\Tests\Fixtures\Models\RichJsonPost;
use Awcodes\Curator\Tests\Fixtures\Models\RichPost;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

// Characterization tests for how 5.x reads the three shapes of saved RichEditor content, stored as HTML or as the
// editor's TipTap JSON, so a rebuild can be checked against them:
//
// A. legacy (up to 5.4): data-id = media key, with the src the media had when it was inserted;
// B. default (5.5 and later): a plain img with no data-id;
// C. media ids (5.6 and later, storeMediaIds()): data-id = media key, with the src it had when last saved.
//
// A and C are the same bytes. Only their src differs, so the tests give A a stale src and C the current one, and
// expect them to resolve the same way. JSON stores the key in the image node's attrs.id.
//
// Filament reads data-id as a file attachment id. Without a provider it treats the id as a path on its own disk, so a
// media key matches nothing. The renderer then drops the src, but the editor keeps the stored one.

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');

    RichPost::$storeMediaIds = false;
});

afterEach(function () {
    RichPost::$storeMediaIds = false;
});

function richShapesMedia(array $overrides = []): Media
{
    return makeMedia(array_merge(['directory' => 'media', 'name' => 'current', 'path' => 'media/current.jpg'], $overrides));
}

function richShapesCurrentUrl(): string
{
    return Storage::disk('public')->url('media/current.jpg');
}

/**
 * One image node in the shape given, for a media key.
 *
 * @return array{type: string, attrs: array<string, mixed>}
 */
function richShapesImageNode(string $shape, mixed $key): array
{
    return ['type' => 'image', 'attrs' => match ($shape) {
        'A' => ['src' => 'https://old.example.com/storage/media/inserted.jpg', 'alt' => 'Dusk', 'title' => 'Mountain dusk', 'id' => $key],
        'B' => ['src' => '/storage/media/inserted.jpg', 'alt' => 'Dusk', 'title' => 'Mountain dusk'],
        'C' => ['src' => richShapesCurrentUrl(), 'alt' => 'Dusk', 'title' => 'Mountain dusk', 'id' => $key],
    }];
}

function richShapesImageHtml(string $shape, mixed $key): string
{
    $attrs = richShapesImageNode($shape, $key)['attrs'];

    return '<img src="' . $attrs['src'] . '" alt="Dusk" title="Mountain dusk"' . (array_key_exists('id', $attrs) ? ' data-id="' . $key . '"' : '') . '>';
}

/**
 * @param  array<int, array{type: string, attrs: array<string, mixed>}>  $images
 * @return array{type: string, content: array<int, mixed>}
 */
function richShapesDocument(array $images): array
{
    return ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => $images]]];
}

/**
 * The content in the storage given: an HTML string, or a TipTap JSON document.
 *
 * @param  array<int, string>  $shapes
 * @return string|array<string, mixed>
 */
function richShapesContent(string $storage, array $shapes, mixed $key): string | array
{
    return $storage === 'json'
        ? richShapesDocument(array_map(fn (string $shape): array => richShapesImageNode($shape, $key), $shapes))
        : '<p>' . implode('', array_map(fn (string $shape): string => richShapesImageHtml($shape, $key), $shapes)) . '</p>';
}

/**
 * The attrs of every image in a TipTap document, in order.
 *
 * @return array<int, array<string, mixed>>
 */
function richShapesImageAttrs(array $document): array
{
    $images = [];

    $walk = function (array $node) use (&$walk, &$images): void {
        if (($node['type'] ?? null) === 'image') {
            $images[] = $node['attrs'] ?? [];
        }

        foreach ($node['content'] ?? [] as $child) {
            $walk($child);
        }
    };

    $walk($document);

    return $images;
}

function richShapesRender(string | array $content, bool $storeMediaIds): string
{
    return RichContentRenderer::make($content)
        ->plugins([AttachCuratorMediaPlugin::make()->storeMediaIds($storeMediaIds)])
        ->toHtml();
}

test('the renderer reads each shape the same from html and from json', function (string $shape, string $storage, bool $storeMediaIds, ?string $expectedSrc) {
    $media = richShapesMedia();

    $expected = $expectedSrc === 'current' ? richShapesCurrentUrl() : $expectedSrc;
    $id = $shape === 'B' ? '' : ' data-id="' . $media->getKey() . '"';
    $src = $expected === null ? '' : 'src="' . $expected . '" ';

    expect(richShapesRender(richShapesContent($storage, [$shape], $media->getKey()), $storeMediaIds))
        ->toBe("<p><img {$src}alt=\"Dusk\" title=\"Mountain dusk\"{$id} /></p>");
})->with([
    'A, html, default' => ['A', 'html', false, null],
    'A, json, default' => ['A', 'json', false, null],
    'A, html, media ids' => ['A', 'html', true, 'current'],
    'A, json, media ids' => ['A', 'json', true, 'current'],
    'B, html, default' => ['B', 'html', false, '/storage/media/inserted.jpg'],
    'B, json, default' => ['B', 'json', false, '/storage/media/inserted.jpg'],
    'B, html, media ids' => ['B', 'html', true, '/storage/media/inserted.jpg'],
    'B, json, media ids' => ['B', 'json', true, '/storage/media/inserted.jpg'],
    'C, html, default' => ['C', 'html', false, null],
    'C, json, default' => ['C', 'json', false, null],
    'C, html, media ids' => ['C', 'html', true, 'current'],
    'C, json, media ids' => ['C', 'json', true, 'current'],
]);

test('a data-id resolves only when it names public media in the library', function (string $storage, string $case) {
    $public = richShapesMedia();
    $private = richShapesMedia(['name' => 'private', 'path' => 'media/private.jpg', 'visibility' => 'private']);
    $deleted = richShapesMedia(['name' => 'deleted', 'path' => 'media/deleted.jpg']);
    $deletedKey = $deleted->getKey();
    $deleted->delete();

    $key = match ($case) {
        'public' => $public->getKey(),
        'private' => $private->getKey(),
        'deleted' => $deletedKey,
        'unknown' => 999,
        'string key' => (string) $public->getKey(),
    };

    $html = richShapesRender(richShapesContent($storage, ['A'], $key), true);

    expect($html)->toContain('data-id="' . $key . '"');

    $case === 'public' || $case === 'string key'
        ? expect($html)->toContain('src="' . richShapesCurrentUrl() . '"')
        : expect($html)->not->toContain('src=');
})->with(['html', 'json'])->with(['public', 'private', 'deleted', 'unknown', 'string key']);

test('a filament attachment path in data-id resolves only without media ids', function (string $storage) {
    config()->set('filament.default_filesystem_disk', 'public');
    Storage::disk('public')->put('attachments/diagram.png', 'png');

    $content = $storage === 'json'
        ? richShapesDocument([['type' => 'image', 'attrs' => ['src' => '/old/diagram.png', 'alt' => 'Diagram', 'id' => 'attachments/diagram.png']]])
        : '<p><img src="/old/diagram.png" alt="Diagram" data-id="attachments/diagram.png"></p>';

    expect(richShapesRender($content, false))->toContain('src="' . Storage::disk('public')->url('attachments/diagram.png') . '"')
        ->and(richShapesRender($content, true))->not->toContain('src=')
        ->and(richShapesRender($content, true))->toContain('data-id="attachments/diagram.png"');
})->with(['html', 'json']);

test('parsing html gives a string media key, json keeps the type it was saved with', function () {
    $media = richShapesMedia();

    $fromHtml = RichContentRenderer::make(richShapesContent('html', ['A'], $media->getKey()))->getEditor()->getDocument();
    $fromJson = RichContentRenderer::make(richShapesContent('json', ['A'], $media->getKey()))->getEditor()->getDocument();

    expect(richShapesImageAttrs($fromHtml)[0]['id'])->toBe((string) $media->getKey())
        ->and(richShapesImageAttrs($fromJson)[0]['id'])->toBe($media->getKey());
});

test('a model rich content attribute renders every shape and leaves the stored value alone', function (string $model, bool $storeMediaIds) {
    RichPost::$storeMediaIds = $storeMediaIds;

    $media = richShapesMedia();
    $storage = $model === RichJsonPost::class ? 'json' : 'html';
    $content = richShapesContent($storage, ['A', 'B', 'C'], $media->getKey());

    /** @var RichPost $post */
    $post = $model::query()->create(['title' => 'Post', 'content' => $content]);
    $post = $post->fresh();
    $raw = $post->getRawOriginal('content');

    $html = $post->renderRichContent('content');
    $key = $media->getKey();
    $resolved = $storeMediaIds ? 'src="' . richShapesCurrentUrl() . '" ' : '';

    expect($html)->toBe("<p><img {$resolved}alt=\"Dusk\" title=\"Mountain dusk\" data-id=\"{$key}\" /><img src=\"/storage/media/inserted.jpg\" alt=\"Dusk\" title=\"Mountain dusk\" /><img {$resolved}alt=\"Dusk\" title=\"Mountain dusk\" data-id=\"{$key}\" /></p>")
        ->and($post->getRichContentAttribute('content')->isJson())->toBe($storage === 'json')
        ->and($post->getRichContentAttribute('content')->getFileAttachmentProvider() instanceof CuratorFileAttachmentProvider)->toBe($storeMediaIds)
        ->and($post->fresh()->getRawOriginal('content'))->toBe($raw);
})->with([
    'html column' => RichPost::class,
    'json column' => RichJsonPost::class,
])->with([
    'default' => false,
    'media ids' => true,
]);

// The editor resolves each data-id through the provider of the model's rich content attribute, and keeps the stored
// src when that finds nothing. So unlike the renderer it shows legacy images, and saving writes their src back.

test('the editor keeps the stored src of every shape without media ids', function (string $model) {
    $media = richShapesMedia();
    $storage = $model === RichJsonPost::class ? 'json' : 'html';

    $post = $model::query()->create(['title' => 'Post', 'content' => richShapesContent($storage, ['A', 'B', 'C'], $media->getKey())]);

    $component = Livewire::test(RichEditorForm::class, ['record' => $post->fresh()]);

    $key = $storage === 'json' ? $media->getKey() : (string) $media->getKey();

    expect(array_map(fn (array $attrs): array => [$attrs['src'], $attrs['id'] ?? null], richShapesImageAttrs($component->get('data.content'))))->toBe([
        ['https://old.example.com/storage/media/inserted.jpg', $key],
        ['/storage/media/inserted.jpg', null],
        [richShapesCurrentUrl(), $key],
    ]);

    $component->call('save');

    expect($component->get('saved'))->toBe(richShapesContent($storage, ['A', 'B', 'C'], $key));
})->with([
    'html column' => RichPost::class,
    'json column' => RichJsonPost::class,
]);

test('with media ids the editor refreshes the src of public media and keeps the stored src of the rest', function (string $model) {
    RichPost::$storeMediaIds = true;

    $public = richShapesMedia();
    $private = richShapesMedia(['name' => 'private', 'path' => 'media/private.jpg', 'visibility' => 'private']);
    $storage = $model === RichJsonPost::class ? 'json' : 'html';

    $content = $storage === 'json'
        ? richShapesDocument([richShapesImageNode('A', $public->getKey()), richShapesImageNode('B', null), richShapesImageNode('A', $private->getKey()), richShapesImageNode('A', 999)])
        : '<p>' . richShapesImageHtml('A', $public->getKey()) . richShapesImageHtml('B', null) . richShapesImageHtml('A', $private->getKey()) . richShapesImageHtml('A', 999) . '</p>';

    $post = $model::query()->create(['title' => 'Post', 'content' => $content]);

    $component = Livewire::test(RichEditorForm::class, ['record' => $post->fresh()]);

    $stale = 'https://old.example.com/storage/media/inserted.jpg';

    expect(array_column(richShapesImageAttrs($component->get('data.content')), 'src'))->toBe([
        richShapesCurrentUrl(),
        '/storage/media/inserted.jpg',
        $stale,
        $stale,
    ]);

    $component->call('save');

    $saved = $component->get('saved');

    $storage === 'json'
        ? expect(array_column(richShapesImageAttrs($saved), 'src'))->toBe([richShapesCurrentUrl(), '/storage/media/inserted.jpg', $stale, $stale])
        : expect($saved)->toBe('<p><img src="' . richShapesCurrentUrl() . '" alt="Dusk" title="Mountain dusk" data-id="' . $public->getKey() . '">'
            . richShapesImageHtml('B', null)
            . richShapesImageHtml('A', $private->getKey())
            . richShapesImageHtml('A', 999) . '</p>');
})->with([
    'html column' => RichPost::class,
    'json column' => RichJsonPost::class,
]);

// docs/fields/rich-editor.md, "Content saved before 5.5", gives this regex to make shape A render without
// storeMediaIds(). It can't tell A from C, since they are the same bytes.

function richShapesStripLegacyIds(string $content): string
{
    return preg_replace(
        '/(<img\b[^>]*?)\s+data-id="(?:\d+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}|[0-9A-HJKMNP-TV-Z]{26})"/i',
        '$1',
        $content,
    );
}

test('the documented regex turns a and c into b and keeps their stored src', function () {
    $media = richShapesMedia();
    $key = $media->getKey();

    expect(richShapesStripLegacyIds(richShapesImageHtml('A', $key)))->toBe('<img src="https://old.example.com/storage/media/inserted.jpg" alt="Dusk" title="Mountain dusk">')
        ->and(richShapesStripLegacyIds(richShapesImageHtml('B', $key)))->toBe(richShapesImageHtml('B', $key))
        ->and(richShapesStripLegacyIds(richShapesImageHtml('C', $key)))->toBe('<img src="' . richShapesCurrentUrl() . '" alt="Dusk" title="Mountain dusk">')
        ->and(richShapesRender(richShapesStripLegacyIds(richShapesImageHtml('A', $key)), false))->toContain('src="https://old.example.com/storage/media/inserted.jpg"');
});

test('the documented regex removes uuid and ulid keys and leaves attachment paths', function (string $id, bool $removed) {
    $html = '<img src="/x.jpg" alt="X" data-id="' . $id . '">';

    expect(richShapesStripLegacyIds($html))->toBe($removed ? '<img src="/x.jpg" alt="X">' : $html);
})->with([
    'integer' => ['42', true],
    'uuid' => ['9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d', true],
    'ulid' => ['01ARZ3NDEKTSV4RRFFQ69G5FAV', true],
    'attachment path' => ['attachments/diagram.png', false],
    'attachment file name' => ['diagram.png', false],
]);
