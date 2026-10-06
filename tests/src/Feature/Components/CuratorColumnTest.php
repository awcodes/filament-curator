<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Awcodes\Curator\Concerns\UrlProvider;
use Awcodes\Curator\Facades\Curator;

test('getResolution returns null by default', function () {
    $column = CuratorColumn::make('media');

    expect($column->getResolution())->toBeNull();
});

test('getResolution returns integer after resolution(200)', function () {
    $column = CuratorColumn::make('media')->resolution(200);

    expect($column->getResolution())->toBe(200);
});

test('getResolution evaluates closure', function () {
    $column = CuratorColumn::make('media')->resolution(fn () => 300);

    expect($column->getResolution())->toBe(300);
});

test('getMediaUrl uses the thumbnail without a resolution', function () {
    $media = makeMedia();

    expect(CuratorColumn::make('media')->imageHeight(40)->getMediaUrl($media))->toBe($media->thumbnailUrl);
});

test('getMediaUrl requests the display size multiplied by the resolution', function () {
    $media = makeMedia();

    $url = CuratorColumn::make('media')->imageWidth(60)->imageHeight(40)->resolution(2)->getMediaUrl($media);

    expect($url)->toContain('w=120')->toContain('h=80')->not->toBe($media->thumbnailUrl);
});

test('getMediaUrl keeps the thumbnail for images Glide cannot resize', function () {
    $media = makeMedia(['path' => 'logo.svg', 'ext' => 'svg', 'type' => 'image/svg+xml']);

    expect(CuratorColumn::make('media')->imageHeight(40)->resolution(2)->getMediaUrl($media))->toBe($media->thumbnailUrl);
});

test('getMediaUrl keeps the thumbnail with a custom url provider', function () {
    Curator::urlProvider(new class implements UrlProvider
    {
        public static function getThumbnailUrl(string $path): string
        {
            return "https://cdn.example.com/thumb/{$path}";
        }

        public static function getMediumUrl(string $path): string
        {
            return "https://cdn.example.com/medium/{$path}";
        }

        public static function getLargeUrl(string $path): string
        {
            return "https://cdn.example.com/large/{$path}";
        }
    });

    $media = makeMedia();

    expect(CuratorColumn::make('media')->imageHeight(40)->resolution(2)->getMediaUrl($media))
        ->toBe('https://cdn.example.com/thumb/test-file.jpg');
});
