<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Awcodes\Curator\CuratorUtils;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Workbench\App\Models\Mediable;
use Workbench\App\Models\Post;
use Workbench\Database\Factories\UserFactory;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('workbench');

        UserFactory::new()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $media = Media::query()->create(CuratorUtils::importMedia(
            path: __DIR__ . '/fixtures/curator-workbench.svg',
            disk: 'public',
            directory: 'workbench',
            alt: 'Curator Workbench landscape',
            title: 'Workbench landscape',
            caption: 'Stored on the local public disk.',
            description: 'Seed media used to exercise Curator storage and rendering.',
        ));

        $post = Post::query()->create([
            'title' => 'Developing Curator',
            'content' => '<p>Edit this post to exercise Curator’s picker and rich-editor attachment tool.</p>',
            'featured_image_id' => $media->getKey(),
        ]);

        Mediable::query()->create([
            'mediable_type' => $post->getMorphClass(),
            'mediable_id' => $post->getKey(),
            'media_id' => $media->getKey(),
            'order' => 1,
        ]);
    }
}
