<?php

declare(strict_types=1);

use Awcodes\Curator\Config\GlideManager;
use League\Glide\Server;

test('getBasePath returns curator by default', function () {
    $manager = new GlideManager();

    expect($manager->getBasePath())->toBe('curator');
});

test('getBasePath returns custom value with slashes trimmed', function () {
    $manager = new GlideManager();
    $manager->basePath('/custom-path/');

    expect($manager->getBasePath())->toBe('custom-path');
});

test('getBasePath trims leading slash from custom value', function () {
    $manager = new GlideManager();
    $manager->basePath('/glide');

    expect($manager->getBasePath())->toBe('glide');
});

test('getToken returns value from config', function () {
    $manager = new GlideManager();

    expect($manager->getToken())->toBe(config('curator.glide_token'));
});

// A checkout that never ran curator:install has no token, and the return type
// error that used to surface said nothing about how to fix it.
test('getToken names the missing token instead of raising a type error', function () {
    config()->set('curator.glide_token', null);

    (new GlideManager())->getToken();
})->throws(Exception::class, 'Curator has no Glide token.');

test('getUrl returns a string URL containing the path', function () {
    $manager = new GlideManager();

    $url = $manager->getUrl('some/image.jpg');

    expect($url)->toBeString()->toContain('some/image.jpg');
});

test('getUrl includes query params when provided', function () {
    $manager = new GlideManager();

    $url = $manager->getUrl('image.jpg', ['w' => 200, 'h' => 200]);

    expect($url)->toBeString()->toContain('image.jpg');
});

test('getServer returns a Glide Server instance', function () {
    $manager = new GlideManager();

    expect($manager->getServer())->toBeInstanceOf(Server::class);
});
