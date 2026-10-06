<?php

declare(strict_types=1);

use Awcodes\Curator\View\Components\Curation;
use Illuminate\Support\Facades\Blade;

function curatedMedia(): Awcodes\Curator\Models\Media
{
    return makeMedia([
        'alt' => 'A curated image',
        'curations' => [
            ['curation' => [
                'key' => 'thumbnail',
                'disk' => 'public',
                'directory' => 'test-file',
                'visibility' => 'public',
                'path' => 'test-file/thumbnail.webp',
                'width' => 200,
                'height' => 200,
            ]],
        ],
    ]);
}

test('renders a curation by media id', function () {
    $media = curatedMedia();

    expect(Blade::render('<x-curator-curation :media="$id" curation="thumbnail" />', ['id' => $media->id]))
        ->toContain('test-file/thumbnail.webp')
        ->toContain('width="200"')
        ->toContain('alt="A curated image"');
});

test('accepts a string key, as UUID and ULID models use', function () {
    $media = curatedMedia();

    expect(new Curation(media: (string) $media->id, curation: 'thumbnail'))
        ->curatedMedia->toHaveKey('path', 'test-file/thumbnail.webp');

    expect(new Curation(media: '01J9Z3M4N5P6Q7R8S9T0V1W2X3', curation: 'thumbnail'))
        ->curatedMedia->toBeNull();
});

test('renders nothing without a curation key', function () {
    $media = curatedMedia();

    expect(trim(Blade::render('<x-curator-curation :media="$media" />', ['media' => $media])))->toBe('');
});

test('renders nothing for an unknown curation or a malformed curations entry', function () {
    $media = makeMedia(['curations' => [['not-a-curation' => true], 'garbage']]);

    expect(trim(Blade::render('<x-curator-curation :media="$media" curation="thumbnail" />', ['media' => $media])))->toBe('')
        ->and($media->hasCuration('thumbnail'))->toBeFalse();
});
