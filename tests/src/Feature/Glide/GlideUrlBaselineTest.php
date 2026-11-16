<?php

declare(strict_types=1);

use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GlideBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Characterization tests: every expected URL, signature and cache path below is
// a literal captured from 5.x on Glide 3, not something rebuilt with Glide's own
// URL builder, so a change to signing, parameter order or cache layout fails here.

beforeEach(function () {
    $this->storagePath = sys_get_temp_dir() . '/curator-glide-url-baseline-' . Str::random(12);
    File::ensureDirectoryExists($this->storagePath . '/app');
    $this->app->useStoragePath($this->storagePath);

    Storage::fake('public');
    Storage::fake('local');

    config()->set('curator.glide_token', 'baseline-token');
    config()->set('curator.default_disk', 'public');
    config()->set('curator.temporary_url_expiration', 5);

    $this->travelTo(Carbon::parse('2026-01-01 12:00:00', 'UTC'));
});

afterEach(function () {
    File::deleteDirectory($this->storagePath);
});

function glideUrlBaselineMedia(array $overrides = []): Media
{
    return makeMedia(array_merge([
        'disk' => 'public',
        'directory' => 'media',
        'name' => 'photo',
        'path' => 'media/photo.png',
        'type' => 'image/png',
        'ext' => 'png',
        'width' => 40,
        'height' => 20,
    ], $overrides));
}

test('a public media item on the default disk has fixed permanent urls', function () {
    $media = glideUrlBaselineMedia();

    expect($media->getGlideUrl())->toBe('/curator/media/photo.png?s=6470ea5c4691d7d1767c6402e5a507b1')
        ->and($media->getGlideUrl(['w' => 100]))->toBe('/curator/media/photo.png?w=100&s=d7d019b62d30bb3e766893fcf219b93f')
        ->and($media->thumbnail_url)->toBe('/curator/media/photo.png?fit=crop&fm=webp&h=200&w=200&s=c4c94bb7c61d8b2d0dbad82df6068b64')
        ->and($media->medium_url)->toBe('/curator/media/photo.png?fit=crop&fm=webp&h=640&w=640&s=658ba4bdaffd06ff04b971068d8077b1')
        ->and($media->large_url)->toBe('/curator/media/photo.png?fit=contain&fm=webp&h=1024&w=1024&s=a1446f382305758b61a5d2149bc3f139');
});

test('a public media item on a non-default disk has fixed permanent urls that do not name the disk', function () {
    $media = glideUrlBaselineMedia(['disk' => 'local', 'path' => 'media/local.png']);

    expect($media->getGlideUrl())->toBe('/curator/media/local.png?s=afdfb538ed36fa1eef49c740bf924dab')
        ->and($media->getGlideUrl(['w' => 100]))->toBe('/curator/media/local.png?w=100&s=2691dfc110e3dd0aabd5cb7c709e190e')
        ->and($media->thumbnail_url)->toBe('/curator/media/local.png?fit=crop&fm=webp&h=200&w=200&s=a8ee6c8693751a1ae8f9986f9ab92ae4')
        ->and($media->medium_url)->toBe('/curator/media/local.png?fit=crop&fm=webp&h=640&w=640&s=aaad3e93ffdc24bcd8551d6ea2c11ca8')
        ->and($media->large_url)->toBe('/curator/media/local.png?fit=contain&fm=webp&h=1024&w=1024&s=23f913ff196c35d7ecf796829d9b5c02');
});

test('a private media item has fixed temporary urls naming expires and disk', function () {
    $media = glideUrlBaselineMedia(['disk' => 'local', 'visibility' => 'private', 'path' => 'media/secret.png']);

    // 2026-01-01 12:05:00 UTC: five minutes after the frozen clock.
    expect($media->getGlideUrl())->toBe('/curator/media/secret.png?expires=1767269100&disk=local&s=52fdada84cca0a6ae443a390ead00323')
        ->and($media->getGlideUrl(['w' => 100]))->toBe('/curator/media/secret.png?w=100&expires=1767269100&disk=local&s=934b7b9fa8b3ffb2bcf8344fe670b05c')
        ->and($media->thumbnail_url)->toBe('/curator/media/secret.png?fit=crop&fm=webp&h=200&w=200&expires=1767269100&disk=local&s=a4e1cbe7d2f74df18b6cbc43f4df6511')
        ->and($media->medium_url)->toBe('/curator/media/secret.png?fit=crop&fm=webp&h=640&w=640&expires=1767269100&disk=local&s=68b1cb891475251643bb46f2c5c6ae4a')
        ->and($media->large_url)->toBe('/curator/media/secret.png?fit=contain&fm=webp&h=1024&w=1024&expires=1767269100&disk=local&s=61aca4076e50646ef49c918f4c056c12');
});

test('a temporary url expiry is rounded up to the next whole minute', function () {
    $this->travelTo(Carbon::parse('2026-01-01 12:00:30', 'UTC'));

    // 2026-01-01 12:06:00 UTC.
    expect(Glide::getTemporaryUrlExpiration()->getTimestamp())->toBe(1767269160)
        ->and(Glide::getTemporaryUrl('media/secret.png', ['w' => 100], disk: 'local'))
        ->toBe('/curator/media/secret.png?w=100&expires=1767269160&disk=local&s=b7502b65259b1b892142d4fc246904bd');
});

test('a temporary url without a disk omits the disk parameter', function () {
    expect(Glide::getTemporaryUrl('media/secret.png', ['w' => 100], 1767269100))
        ->toBe('/curator/media/secret.png?w=100&expires=1767269100&s=bbf85c71ff3ac802cb786d4a84cc1ae5');
});

test('glide builder presets produce fixed urls', function () {
    expect(GlideBuilder::make()->width(300)->height(150)->quality(75)->format('jpg')->fit('crop')->toUrl('media/photo.png'))
        ->toBe('/curator/media/photo.png?fit=crop&fm=jpg&h=150&q=75&w=300&s=55b47a00ffd395ea3d12f222e0f6fceb')
        ->and(glide_builder()->width(300)->height(150)->quality(75)->format('jpg')->fit('crop')->toUrl('media/photo.png'))
        ->toBe('/curator/media/photo.png?fit=crop&fm=jpg&h=150&q=75&w=300&s=55b47a00ffd395ea3d12f222e0f6fceb');
});

test('every glide builder param keeps its name and position in the url', function () {
    $builder = GlideBuilder::make()
        ->watermarkFit('crop')
        ->watermarkAlpha(50)
        ->watermarkPosition('bottom-right')
        ->watermarkPadding(5)
        ->watermarkYOffset(4)
        ->watermarkXOffset(3)
        ->watermarkHeight(20)
        ->watermarkWidth(30)
        ->watermarkPath('mark.png')
        ->width(300)
        ->sharpen(10)
        ->pixelate(2)
        ->orientation(90)
        ->quality(80)
        ->height(150)
        ->gamma(1.5)
        ->format('webp')
        ->flip('h')
        ->fit('crop-center')
        ->filter('greyscale')
        ->dpr(2)
        ->crop(100, 50, 10, 5)
        ->contrast(20)
        ->brightness(-10)
        ->border(2, 'ff0000', 'overlay')
        ->blur(5)
        ->background('ffffff');

    expect($builder->toQueryString())
        ->toBe('bg=ffffff&blur=5&border=2%2Cff0000%2Coverlay&bri=-10&con=20&crop=100%2C50%2C10%2C5&dpr=2&filt=greyscale&fit=crop-center&flip=h&fm=webp&gam=1.5&h=150&q=80&or=90&pixel=2&sharp=10&w=300&mark=mark.png&markw=30&markh=20&markx=3&marky=4&markpad=5&markpos=bottom-right&markalpha=50&markfit=crop')
        ->and($builder->toUrl('media/photo.png'))
        ->toBe('/curator/media/photo.png?bg=ffffff&blur=5&border=2%2Cff0000%2Coverlay&bri=-10&con=20&crop=100%2C50%2C10%2C5&dpr=2&filt=greyscale&fit=crop-center&flip=h&fm=webp&gam=1.5&h=150&q=80&or=90&pixel=2&sharp=10&w=300&mark=mark.png&markw=30&markh=20&markx=3&marky=4&markpad=5&markpos=bottom-right&markalpha=50&markfit=crop&s=a0dc855be899af2682635cbb6aff1647');
});

test('the glider renders a fixed src and srcset for public media', function () {
    $html = Blade::render(
        '<x-curator-glider :media="$media" width="20" :srcset="[\'20w\', \'40w\', \'2x\']" sizes="20px" />',
        ['media' => glideUrlBaselineMedia()],
    );

    expect($html)
        ->toContain('src="/curator/media/photo.png?w=20&amp;s=0b6f81bc67f3a5e940f94ec501882bc8"')
        ->toContain('width="20"')
        ->toContain('height="10"')
        ->toContain('srcset="/curator/media/photo.png?h=10&amp;w=20&amp;s=ee9e4bfcb1277e0ded0269b220e4b21c 20w, /curator/media/photo.png?h=20&amp;w=40&amp;s=cf08f38b230271a2a02dd61d271c53a2 40w, /curator/media/photo.png?dpr=2&amp;w=20&amp;s=6510325023f066620f8ae907ea95b6bc 2x"')
        ->toContain('sizes="20px"');
});

test('the glider renders a fixed temporary src and srcset for private media', function () {
    $html = Blade::render(
        '<x-curator-glider :media="$media" width="20" :srcset="[\'20w\']" sizes="20px" />',
        ['media' => glideUrlBaselineMedia(['disk' => 'local', 'visibility' => 'private', 'path' => 'media/secret.png'])],
    );

    expect($html)
        ->toContain('src="/curator/media/secret.png?w=20&amp;expires=1767269100&amp;disk=local&amp;s=0d6f96ccb193a5798435387f60bc7f76"')
        ->toContain('srcset="/curator/media/secret.png?h=10&amp;w=20&amp;expires=1767269100&amp;disk=local&amp;s=16a327244288f039678ba9680af0b78b 20w"');
});

test('the default disk keeps its glide cache directly under .cache', function () {
    expect(Glide::getCachePathPrefix())->toBe('.cache')
        ->and(Glide::getCachePathPrefix('public'))->toBe('.cache')
        ->and(Glide::getServer()->getCachePath('media/photo.png', ['w' => 20]))->toBe('.cache/media/photo.png/714e54d76d54f029')
        ->and(Glide::getServer('public')->getCachePath('media/photo.png', ['fit' => 'crop', 'fm' => 'webp', 'h' => 200, 'w' => 200]))->toBe('.cache/media/photo.png/6634de239186743f');
});

test('a non-default disk keeps its glide cache under a slug and hash of the disk name', function () {
    expect(Glide::getCachePathPrefix('local'))->toBe('.cache/.disks/local-f6a8e84f')
        ->and(Glide::getCachePathPrefix('S3 Media'))->toBe('.cache/.disks/s3-media-1ba8d474')
        ->and(Glide::getServer('local')->getCachePath('media/photo.png', ['w' => 20]))->toBe('.cache/.disks/local-f6a8e84f/media/photo.png/714e54d76d54f029');
});
