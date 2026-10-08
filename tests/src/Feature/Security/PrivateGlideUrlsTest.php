<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Awcodes\Curator\Concerns\UrlProvider;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Glide\GlideBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Glide\Urls\UrlBuilderFactory;

// Glide caches transformed images under storage_path(), so point that at a
// directory of the test's own instead of the shared Testbench skeleton.
beforeEach(function () {
    $this->storagePath = sys_get_temp_dir() . '/curator-private-glide-' . Str::random(12);
    File::ensureDirectoryExists($this->storagePath . '/app');
    $this->app->useStoragePath($this->storagePath);

    Storage::fake('public');
    Storage::fake('local');
});

afterEach(function () {
    File::deleteDirectory($this->storagePath);
});

function storeImage(string $disk, string $path, int $width = 40, int $height = 20): void
{
    Storage::disk($disk)->put($path, UploadedFile::fake()->image(basename($path), $width, $height)->getContent());
}

function imageMedia(array $overrides = []): Media
{
    return makeMedia(array_merge([
        'disk' => 'public',
        'name' => 'photo',
        'path' => 'photo.png',
        'type' => 'image/png',
        'ext' => 'png',
    ], $overrides));
}

function permanentGlideUrl(string $path, array $params = []): string
{
    return UrlBuilderFactory::create(Glide::getBasePath(), Glide::getToken())->getUrl($path, $params);
}

function queryParams(string $url): array
{
    parse_str((string) parse_url($url, PHP_URL_QUERY), $params);

    return $params;
}

test('public media keeps the permanent size urls it has always had', function () {
    $media = imageMedia();

    expect($media->thumbnail_url)->toBe(permanentGlideUrl('photo.png', ['fit' => 'crop', 'fm' => 'webp', 'h' => 200, 'w' => 200]))
        ->and($media->medium_url)->toBe(permanentGlideUrl('photo.png', ['fit' => 'crop', 'fm' => 'webp', 'h' => 640, 'w' => 640]))
        ->and($media->large_url)->toBe(permanentGlideUrl('photo.png', ['fit' => 'contain', 'fm' => 'webp', 'h' => 1024, 'w' => 1024]))
        ->and(queryParams($media->thumbnail_url))->not->toHaveKeys(['expires', 'disk']);
});

test('public media is still served with long-lived public cache headers', function () {
    storeImage('public', 'photo.png');
    $media = imageMedia();

    $response = $this->get($media->thumbnail_url);

    $response->assertOk();

    expect($response->headers->get('Cache-Control'))->toContain('public')->toContain('max-age=31536000');
});

test('the size urls of private media expire', function () {
    $this->freezeTime();
    $media = imageMedia(['disk' => 'local', 'visibility' => 'private']);

    foreach ([$media->thumbnail_url, $media->medium_url, $media->large_url] as $url) {
        $params = queryParams($url);

        expect($params)->toHaveKey('disk', 'local')
            ->and((int) $params['expires'])->toBeGreaterThan(now()->getTimestamp())
            ->toBeLessThanOrEqual(now()->addMinutes(6)->getTimestamp());
    }
});

test('the lifetime of temporary urls is configurable', function () {
    $this->freezeTime();
    config()->set('curator.temporary_url_expiration', 30);

    $expires = (int) queryParams(imageMedia(['visibility' => 'private'])->thumbnail_url)['expires'];

    expect($expires)->toBeGreaterThanOrEqual(now()->addMinutes(30)->getTimestamp())
        ->toBeLessThanOrEqual(now()->addMinutes(31)->getTimestamp());
});

test('a private image is served through its size url with private cache headers', function () {
    storeImage('local', 'photo.png');
    $media = imageMedia(['disk' => 'local', 'visibility' => 'private']);

    $response = $this->get($media->thumbnail_url);

    $response->assertOk();

    expect($response->headers->get('Cache-Control'))->toContain('private')
        ->not->toContain('public')
        ->not->toContain('31536000')
        ->and($response->headers->has('Expires'))->toBeFalse();
});

test('a private image size url is refused once it expires', function () {
    storeImage('local', 'photo.png');
    $media = imageMedia(['disk' => 'local', 'visibility' => 'private']);
    $url = $media->thumbnail_url;

    $this->get($url)->assertOk();

    $this->travel(10)->minutes();

    $this->get($url)->assertForbidden();
});

test('a private image size url is refused when its expiry is changed', function () {
    storeImage('local', 'photo.png');
    $media = imageMedia(['disk' => 'local', 'visibility' => 'private']);
    $url = $media->thumbnail_url;
    $expires = queryParams($url)['expires'];

    $this->get(str_replace('expires=' . $expires, 'expires=' . ($expires + 86400), $url))->assertForbidden();
    $this->get(str_replace('expires=' . $expires, 'expires=9999999999', $url))->assertForbidden();
});

test('a permanent url never serves private media', function () {
    storeImage('local', 'photo.png');
    imageMedia(['disk' => 'local', 'visibility' => 'private']);

    $this->get(permanentGlideUrl('photo.png', ['fit' => 'crop', 'fm' => 'webp', 'h' => 200, 'w' => 200]))->assertNotFound();
    $this->get(permanentGlideUrl('photo.png'))->assertNotFound();
});

test('a private pdf is only streamed through an unexpired url', function () {
    Storage::disk('local')->put('docs/report.pdf', '%PDF-1.4 private report');

    $media = makeMedia([
        'disk' => 'local',
        'visibility' => 'private',
        'name' => 'report',
        'path' => 'docs/report.pdf',
        'type' => 'application/pdf',
        'ext' => 'pdf',
    ]);

    $this->get(permanentGlideUrl('docs/report.pdf', ['fit' => 'crop', 'fm' => 'webp', 'h' => 200, 'w' => 200]))->assertNotFound();

    $url = $media->thumbnail_url;
    $response = $this->get($url);

    $response->assertOk();

    expect($response->streamedContent())->toBe('%PDF-1.4 private report')
        ->and($response->headers->get('Cache-Control'))->toContain('private')->not->toContain('public');

    $this->travel(10)->minutes();

    $this->get($url)->assertForbidden();
});

test('a path stored on two disks resolves to the record the url was made for', function () {
    storeImage('public', 'shared/photo.png', 40, 20);
    storeImage('local', 'shared/photo.png', 30, 30);

    $public = imageMedia(['path' => 'shared/photo.png']);
    $private = imageMedia(['disk' => 'local', 'visibility' => 'private', 'path' => 'shared/photo.png']);

    // Request the private copy first, so a cache shared between disks would hand it to the public url.
    $privateResponse = $this->get($private->large_url);
    $publicResponse = $this->get($public->large_url);

    $privateResponse->assertOk();
    $publicResponse->assertOk();

    [$privateWidth, $privateHeight] = getimagesizefromstring($privateResponse->streamedContent());
    [$publicWidth, $publicHeight] = getimagesizefromstring($publicResponse->streamedContent());

    expect($privateWidth)->toBe($privateHeight)
        ->and($publicWidth)->toBe($publicHeight * 2);
});

test('a permanent url resolves to the public record when a private one shares its path', function () {
    Storage::disk('local')->put('shared/report.pdf', '%PDF-1.4 private');
    Storage::disk('public')->put('shared/report.pdf', '%PDF-1.4 public');

    makeMedia(['disk' => 'local', 'visibility' => 'private', 'path' => 'shared/report.pdf', 'type' => 'application/pdf', 'ext' => 'pdf']);
    makeMedia(['disk' => 'public', 'path' => 'shared/report.pdf', 'type' => 'application/pdf', 'ext' => 'pdf']);

    expect($this->get(permanentGlideUrl('shared/report.pdf'))->streamedContent())->toBe('%PDF-1.4 public');
});

test('a custom url provider built on glide gets temporary urls for private media', function () {
    Curator::urlProvider(new class implements UrlProvider
    {
        public static function getThumbnailUrl(string $path): string
        {
            return GlideBuilder::make()->width(10)->toUrl($path);
        }

        public static function getMediumUrl(string $path): string
        {
            return GlideBuilder::make()->width(20)->toUrl($path);
        }

        public static function getLargeUrl(string $path): string
        {
            return GlideBuilder::make()->width(30)->toUrl($path);
        }
    });

    expect(queryParams(imageMedia(['visibility' => 'private'])->thumbnail_url))->toHaveKeys(['expires', 'disk'])
        ->and(queryParams(imageMedia(['path' => 'other.png'])->thumbnail_url))->not->toHaveKey('expires')
        ->and(queryParams(Glide::getUrl('loose.png')))->not->toHaveKey('expires');
});

test('the glider renders temporary urls for private media and permanent ones for public media', function () {
    $private = imageMedia(['disk' => 'local', 'visibility' => 'private', 'width' => 40, 'height' => 20]);
    $public = imageMedia(['path' => 'public.png', 'width' => 40, 'height' => 20]);

    $privateHtml = html_entity_decode(Blade::render('<x-curator-glider :media="$media" width="20" :srcset="[\'20w\']" sizes="20px" />', ['media' => $private]));
    $byIdHtml = html_entity_decode(Blade::render('<x-curator-glider :media="$id" width="20" />', ['id' => $private->id]));
    $publicHtml = html_entity_decode(Blade::render('<x-curator-glider :media="$media" width="20" />', ['media' => $public]));

    preg_match('/src="([^"]+)"/', $privateHtml, $src);
    preg_match('/srcset="([^ "]+)/', $privateHtml, $srcset);

    expect(queryParams($src[1]))->toHaveKeys(['expires', 'disk'])
        ->and(queryParams($srcset[1]))->toHaveKeys(['expires', 'disk'])
        ->and($byIdHtml)->toContain('expires=')
        ->and($publicHtml)->toContain('src="' . permanentGlideUrl('public.png', ['w' => '20']) . '"');
});

test('the table column renders temporary urls for private media', function () {
    $column = CuratorColumn::make('media')->imageHeight(40)->resolution(2);

    expect(queryParams($column->getMediaUrl(imageMedia(['visibility' => 'private']))))->toHaveKey('expires')
        ->and(queryParams($column->getMediaUrl(imageMedia(['path' => 'public.png']))))->not->toHaveKey('expires');
});

test('a private curation renders a temporary url', function () {
    Storage::disk('local')->buildTemporaryUrlsUsing(
        fn (string $path, DateTimeInterface $expiration): string => 'https://files.test/' . $path . '?expires=' . $expiration->getTimestamp()
    );

    $media = imageMedia([
        'disk' => 'local',
        'visibility' => 'private',
        'curations' => [
            ['curation' => [
                'key' => 'thumbnail',
                'disk' => 'local',
                'directory' => 'photo',
                'visibility' => 'private',
                'path' => 'photo/thumbnail.webp',
                'width' => 200,
                'height' => 200,
            ]],
        ],
    ]);

    expect(Blade::render('<x-curator-curation :media="$media" curation="thumbnail" />', ['media' => $media]))
        ->toContain('https://files.test/photo/thumbnail.webp?expires=');
});

test('deleting media clears the glide cache kept for its disk', function () {
    storeImage('local', 'photo.png');
    $media = imageMedia(['disk' => 'local', 'visibility' => 'private']);

    $this->get($media->thumbnail_url)->assertOk();

    $cacheFolder = storage_path('app/' . Glide::getCachePathPrefix('local') . '/photo.png');

    expect(File::isDirectory($cacheFolder))->toBeTrue();

    $media->delete();

    expect(File::isDirectory($cacheFolder))->toBeFalse();
});
