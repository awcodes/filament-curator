<?php

declare(strict_types=1);

use Awcodes\Curator\Resources\Media\Pages\EditMedia;
use Livewire\Livewire;
use Workbench\App\Filament\Resources\Posts\Pages\EditPost;
use Workbench\App\Models\Mediable;
use Workbench\App\Models\Post;

// `size`, `width` and `height` are nullable in stubs/migration.stub, so any view
// that displays them has to tolerate null rather than handing it straight to
// sizeForHumans(int $size). See https://github.com/awcodes/filament-curator/issues/721.

test('the media edit page renders a record with a null size', function () {
    $media = makeMedia(['name' => 'no-size', 'size' => null, 'width' => null, 'height' => null]);

    Livewire::test(EditMedia::class, ['record' => $media->id])
        ->assertOk();
});

test('the picker list display renders an item with a null size', function () {
    config()->set('curator_testing.picker_list_display', true);

    $post = Post::create(['title' => 'Null size post']);
    $media = makeMedia(['name' => 'no-size', 'size' => null, 'width' => null, 'height' => null]);

    Mediable::create([
        'mediable_type' => Post::class,
        'mediable_id' => $post->id,
        'media_id' => $media->id,
        'order' => 1,
    ]);

    Livewire::test(EditPost::class, ['record' => $post->id])
        ->assertOk();
});
