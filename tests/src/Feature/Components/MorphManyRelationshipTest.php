<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Workbench\App\Filament\Resources\Posts\Pages\EditPost;
use Workbench\App\Models\Mediable;
use Workbench\App\Models\Post;

function makePost(array $overrides = []): Post
{
    return Post::create(array_merge(['title' => 'Test Post'], $overrides));
}

function makeMedium(array $overrides = []): Media
{
    return Media::create(array_merge([
        'disk' => 'public',
        'directory' => null,
        'visibility' => 'public',
        'name' => 'test-image',
        'path' => 'test-' . uniqid() . '.jpg',
        'width' => 800,
        'height' => 600,
        'size' => 1000,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ], $overrides));
}

function attachMediable(Post $post, Media $media, int $order, ?string $type = null): Mediable
{
    return Mediable::create([
        'mediable_type' => Post::class,
        'mediable_id' => $post->id,
        'media_id' => $media->id,
        'order' => $order,
        'type' => $type,
    ]);
}

test('MorphMany: initial save assigns sort order starting at 1', function () {
    Storage::fake('public');

    $post = makePost();
    $media1 = makeMedium(['name' => 'image-a']);
    $media2 = makeMedium(['name' => 'image-b']);
    $media3 = makeMedium(['name' => 'image-c']);

    // Inject state directly into the Livewire component, bypassing fillForm's
    // array-flattening which is incompatible with CuratorPicker's UUID-keyed format.
    Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->set('data.gallery', [
            (string) Str::uuid() => $media1->toArray(),
            (string) Str::uuid() => $media2->toArray(),
            (string) Str::uuid() => $media3->toArray(),
        ])
        ->call('save');

    expect(Mediable::where('mediable_id', $post->id)->count())->toBe(3);

    $rows = Mediable::where('mediable_id', $post->id)
        ->pluck('order', 'media_id')
        ->toArray();

    expect($rows[$media1->id])->toBe(1)
        ->and($rows[$media2->id])->toBe(2)
        ->and($rows[$media3->id])->toBe(3);
});

test('MorphMany: re-save updates all items sort order, not just the first', function () {
    Storage::fake('public');

    $post = makePost();
    $media1 = makeMedium(['name' => 'image-a']);
    $media2 = makeMedium(['name' => 'image-b']);
    $media3 = makeMedium(['name' => 'image-c']);

    // Pre-create mediable records in a known order (1 → media1, 2 → media2, 3 → media3).
    // loadStateFromRelationshipsUsing will load them in insertion order: [m1, m2, m3].
    attachMediable($post, $media1, 1);
    attachMediable($post, $media2, 2);
    attachMediable($post, $media3, 3);

    // Mount and re-save with items deliberately reordered: [media3, media1, media2].
    // The bug caused only the first item's order to be updated; the others were skipped
    // because the MorphMany builder accumulated stale WHERE clauses.
    Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->set('data.gallery', [
            (string) Str::uuid() => $media3->toArray(),
            (string) Str::uuid() => $media1->toArray(),
            (string) Str::uuid() => $media2->toArray(),
        ])
        ->call('save');

    $rows = Mediable::where('mediable_id', $post->id)
        ->pluck('order', 'media_id')
        ->toArray();

    // All three items must have their order updated — not just the first
    expect($rows[$media3->id])->toBe(1)
        ->and($rows[$media1->id])->toBe(2)
        ->and($rows[$media2->id])->toBe(3);
});

test('MorphMany: re-saving same items resets sort order to start at 1', function () {
    Storage::fake('public');

    $post = makePost();
    $media1 = makeMedium(['name' => 'image-a']);
    $media2 = makeMedium(['name' => 'image-b']);
    $media3 = makeMedium(['name' => 'image-c']);

    // Pre-create with high order values to detect the old cumulative-start bug.
    attachMediable($post, $media1, 10);
    attachMediable($post, $media2, 20);
    attachMediable($post, $media3, 30);

    // Re-save the same items in the same order.
    Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->set('data.gallery', [
            (string) Str::uuid() => $media1->toArray(),
            (string) Str::uuid() => $media2->toArray(),
            (string) Str::uuid() => $media3->toArray(),
        ])
        ->call('save');

    $rows = Mediable::where('mediable_id', $post->id)
        ->pluck('order', 'media_id')
        ->toArray();

    // Order must start at 1, not continue from where old values left off
    expect($rows[$media1->id])->toBe(1)
        ->and($rows[$media2->id])->toBe(2)
        ->and($rows[$media3->id])->toBe(3);
});

test('MorphMany: saving removes deselected items', function () {
    Storage::fake('public');

    $post = makePost();
    $media1 = makeMedium(['name' => 'image-a']);
    $media2 = makeMedium(['name' => 'image-b']);
    $media3 = makeMedium(['name' => 'image-c']);

    attachMediable($post, $media1, 1);
    attachMediable($post, $media2, 2);
    attachMediable($post, $media3, 3);

    // Re-save with only two items — media2 should be removed.
    Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->set('data.gallery', [
            (string) Str::uuid() => $media1->toArray(),
            (string) Str::uuid() => $media3->toArray(),
        ])
        ->call('save');

    expect(Mediable::where('mediable_id', $post->id)->count())->toBe(2);
    expect(Mediable::where(['mediable_id' => $post->id, 'media_id' => $media2->id])->exists())->toBeFalse();
});

test('MorphMany: a typed picker only updates its own type on save', function () {
    Storage::fake('public');

    $post = makePost();
    $media = makeMedium(['name' => 'shared']);

    // The same medium selected in two typed pickers on the same relationship.
    // The type-scoped update must not rewrite the sibling row's type, which
    // previously orphaned it and made the next picker create a duplicate.
    foreach (range(1, 3) as $ignored) {
        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->set('data.featured', [(string) Str::uuid() => $media->toArray()])
            ->set('data.thumb', [(string) Str::uuid() => $media->toArray()])
            ->call('save');
    }

    $rows = Mediable::where('mediable_id', $post->id)->get();

    expect($rows)->toHaveCount(2)
        ->and($rows->where('type', 'featured')->count())->toBe(1)
        ->and($rows->where('type', 'thumb')->count())->toBe(1);
});

test('MorphMany: an untyped picker ignores rows belonging to typed pickers', function () {
    Storage::fake('public');

    $post = makePost();
    $media1 = makeMedium(['name' => 'image-a']);
    $media2 = makeMedium(['name' => 'image-b']);

    attachMediable($post, $media1, 1, 'featured');
    attachMediable($post, $media2, 1, 'thumb');

    // The untyped 'gallery' picker shares the relationship with the typed pickers.
    // It must load nothing here, and saving must not copy the typed rows into
    // untyped duplicates of itself.
    Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertSet('data.gallery', [])
        ->call('save');

    $rows = Mediable::where('mediable_id', $post->id)->get();

    expect($rows)->toHaveCount(2)
        ->and($rows->whereNull('type')->count())->toBe(0)
        ->and($rows->firstWhere('type', 'featured')->media_id)->toBe($media1->id)
        ->and($rows->firstWhere('type', 'thumb')->media_id)->toBe($media2->id);
});

test('MorphMany: a typed picker ignores untyped rows on the same relationship', function () {
    Storage::fake('public');

    $post = makePost();
    $untyped = makeMedium(['name' => 'image-a']);
    $featured = makeMedium(['name' => 'image-b']);

    attachMediable($post, $untyped, 1);

    Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertSet('data.featured', [])
        ->set('data.featured', [(string) Str::uuid() => $featured->toArray()])
        ->call('save');

    $rows = Mediable::where('mediable_id', $post->id)->get();

    // The untyped row survives: the typed picker's delete is scoped to its own type.
    expect($rows)->toHaveCount(2)
        ->and($rows->firstWhere('type', null)->media_id)->toBe($untyped->id)
        ->and($rows->firstWhere('type', 'featured')->media_id)->toBe($featured->id);
});

test('MorphMany: a falsy-but-set type value is written to new rows', function () {
    Storage::fake('public');

    $post = makePost();
    $media = makeMedium(['name' => 'image-a']);

    // '0' is falsy but a legitimate type, e.g. a backed enum's zero case. It must be
    // persisted, otherwise the row can never be matched by the type-scoped lookup and
    // every save creates another duplicate.
    foreach (range(1, 2) as $ignored) {
        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->set('data.numericType', [(string) Str::uuid() => $media->toArray()])
            ->call('save');
    }

    $rows = Mediable::where('mediable_id', $post->id)->get();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->type)->toBe('0');
});
