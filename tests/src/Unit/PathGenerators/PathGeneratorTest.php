<?php

declare(strict_types=1);

use Awcodes\Curator\PathGenerators\DatePathGenerator;
use Awcodes\Curator\PathGenerators\DefaultPathGenerator;
use Awcodes\Curator\PathGenerators\UserPathGenerator;
use Carbon\Carbon;

test('DefaultPathGenerator returns empty string when no base dir', function () {
    $generator = new DefaultPathGenerator();

    expect($generator->getPath(null))->toBe('');
});

test('DefaultPathGenerator returns base dir as-is', function () {
    $generator = new DefaultPathGenerator();

    expect($generator->getPath('media'))->toBe('media');
});

test('DatePathGenerator returns Y/m/d with no prefix when base dir is null', function () {
    $generator = new DatePathGenerator();
    $now = Carbon::now();

    $expected = $now->format('Y').'/'.$now->format('m').'/'.$now->format('d');

    expect($generator->getPath(null))->toBe($expected);
});

test('DatePathGenerator returns base dir prefixed with Y/m/d', function () {
    $generator = new DatePathGenerator();
    $now = Carbon::now();

    $expected = 'media/'.$now->format('Y').'/'.$now->format('m').'/'.$now->format('d');

    expect($generator->getPath('media'))->toBe($expected);
});

test('DatePathGenerator omits blank prefix for empty string', function () {
    $generator = new DatePathGenerator();
    $now = Carbon::now();

    $expected = $now->format('Y').'/'.$now->format('m').'/'.$now->format('d');

    expect($generator->getPath(''))->toBe($expected);
});

test('DatePathGenerator omits blank prefix for "0" string', function () {
    $generator = new DatePathGenerator();
    $now = Carbon::now();

    $expected = $now->format('Y').'/'.$now->format('m').'/'.$now->format('d');

    expect($generator->getPath('0'))->toBe($expected);
});

test('UserPathGenerator returns auth user id when no base dir', function () {
    $generator = new UserPathGenerator();
    $userId = auth()->user()->getAuthIdentifier();

    expect($generator->getPath(null))->toBe((string) $userId);
});

test('UserPathGenerator returns base dir prefixed with user id', function () {
    $generator = new UserPathGenerator();
    $userId = auth()->user()->getAuthIdentifier();

    expect($generator->getPath('media'))->toBe('media/'.$userId);
});
