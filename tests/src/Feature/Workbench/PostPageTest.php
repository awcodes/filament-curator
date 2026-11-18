<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Workbench\App\Models\Post;
use Workbench\Database\Seeders\DatabaseSeeder;

// The Workbench's public posts pages render Curator's front-end output from the seeded data.

beforeEach(function () {
    // Glide writes its cache under storage_path('app'), so keep it out of the Testbench skeleton.
    $this->storagePath = sys_get_temp_dir() . '/curator-workbench-posts-' . Str::random(12);
    File::ensureDirectoryExists($this->storagePath . '/app');
    $this->app->useStoragePath($this->storagePath);

    Storage::fake('public');
    config()->set('curator.glide_token', 'workbench-test-token');

    $this->seed(DatabaseSeeder::class);
});

afterEach(function () {
    File::deleteDirectory($this->storagePath);
});

test('the index lists every post', function () {
    $this->get('/posts')
        ->assertOk()
        ->assertSeeInOrder(Post::query()->oldest('id')->pluck('title')->all());
});

test('a post renders its featured image, gallery, curation, size urls and rich content shapes', function () {
    $post = Post::query()->where('title', 'Notes from the mountain trail')->firstOrFail();
    $featured = $post->featuredImage;

    $response = $this->get("/posts/{$post->getKey()}")->assertOk();

    $response->assertSee('srcset="', false)
        ->assertSee('sizes="(max-width: 1024px) 100vw, 1024px"', false)
        ->assertSee('fm=webp', false)
        ->assertSee($featured->thumbnail_url)
        ->assertSee($featured->medium_url)
        ->assertSee($featured->large_url)
        ->assertSee('/forest-hills/post_thumbnail.webp"', false)
        ->assertSee('data-focus="rich-content-shapes"', false)
        ->assertDontSee('src="/storage/moved-since-insert.jpg"', false);

    foreach ($post->galleryMedia as $media) {
        $response->assertSee(e($media->caption), false);
    }

    expect(substr_count((string) $response->getContent(), 'src="' . Storage::disk('public')->url($featured->path) . '"'))->toBeGreaterThanOrEqual(3);
});

test('the seeded curation is a file in the media item\'s curation folder', function () {
    $media = Media::query()->where('name', 'forest-hills')->firstOrFail();
    $curation = $media->getCuration('post_thumbnail');

    expect($curation)->toMatchArray([
        'key' => 'post_thumbnail',
        'directory' => 'forest-hills',
        'path' => '/forest-hills/post_thumbnail.webp',
        'width' => 320,
        'height' => 180,
        'type' => 'image/webp',
    ])
        ->and($media->canResolveCurationFile($curation['disk'], $curation['path']))->toBeTrue()
        ->and(getimagesizefromstring(Storage::disk('public')->get('forest-hills/post_thumbnail.webp')))->toMatchArray([0 => 320, 1 => 180]);
});

test('the glide urls on a post page are served', function () {
    $post = Post::query()->where('title', 'Notes from the mountain trail')->firstOrFail();

    preg_match_all('#/curator/[^"\s]+#', (string) $this->get("/posts/{$post->getKey()}")->getContent(), $matches);

    $urls = array_unique(array_map(html_entity_decode(...), $matches[0]));

    expect($urls)->not->toBeEmpty();

    foreach (array_slice($urls, 0, 4) as $url) {
        $this->get($url)->assertOk();
    }
});
