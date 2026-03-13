<?php

declare(strict_types=1);

use Awcodes\Curator\Enums\AudioExtensions;
use Awcodes\Curator\Enums\PreviewableExtensions;
use Awcodes\Curator\Enums\VideoExtensions;

test('AudioExtensions includes mp3', function () {
    expect(AudioExtensions::toArray())->toContain('mp3');
});

test('AudioExtensions includes wav', function () {
    expect(AudioExtensions::toArray())->toContain('wav');
});

test('AudioExtensions includes ogg', function () {
    expect(AudioExtensions::toArray())->toContain('ogg');
});

test('VideoExtensions includes mp4', function () {
    expect(VideoExtensions::toArray())->toContain('mp4');
});

test('VideoExtensions includes mov', function () {
    expect(VideoExtensions::toArray())->toContain('mov');
});

test('VideoExtensions includes avi', function () {
    expect(VideoExtensions::toArray())->toContain('avi');
});

test('PreviewableExtensions includes jpg', function () {
    expect(PreviewableExtensions::toArray())->toContain('jpg');
});

test('PreviewableExtensions includes png', function () {
    expect(PreviewableExtensions::toArray())->toContain('png');
});

test('PreviewableExtensions includes gif', function () {
    expect(PreviewableExtensions::toArray())->toContain('gif');
});

test('PreviewableExtensions includes svg', function () {
    expect(PreviewableExtensions::toArray())->toContain('svg');
});
