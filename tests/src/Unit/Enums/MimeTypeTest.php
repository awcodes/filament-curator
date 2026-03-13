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
