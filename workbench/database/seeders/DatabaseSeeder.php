<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Workbench\App\Models\Mediable;
use Workbench\App\Models\Post;
use Workbench\Database\Factories\UserFactory;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The fixture images, drawn with GD for the Workbench, oldest first. The media library lists the newest first.
     *
     * @var array<string, array{title: string, alt: string, caption: string}>
     */
    private const MEDIA = [
        'paper-bands' => [
            'title' => 'Paper bands',
            'alt' => 'Layered teal and amber paper bands',
            'caption' => 'Wavy bands from deep teal to amber.',
        ],
        'geometric-circles' => [
            'title' => 'Geometric circles',
            'alt' => 'Overlapping translucent circles on indigo',
            'caption' => 'Five translucent circles on an indigo gradient.',
        ],
        'lake-reflection' => [
            'title' => 'Lake reflection',
            'alt' => 'Crimson hills and a pale sun reflected in a lake',
            'caption' => 'A rose sky mirrored in still water.',
        ],
        'northern-lights' => [
            'title' => 'Northern lights',
            'alt' => 'Green and violet aurora over dark hills',
            'caption' => 'Aurora ribbons over a starry night sky.',
        ],
        'forest-hills' => [
            'title' => 'Forest hills',
            'alt' => 'Rolling green hills under a pale sky',
            'caption' => 'Four ridges of rolling green hills.',
        ],
        'desert-dunes' => [
            'title' => 'Desert dunes',
            'alt' => 'Orange sand dunes beneath a low sun',
            'caption' => 'Dunes in the afternoon heat.',
        ],
        'ocean-horizon' => [
            'title' => 'Ocean horizon',
            'alt' => 'A low sun over a calm blue sea',
            'caption' => 'Sunlight on a calm sea.',
        ],
        'mountain-dusk' => [
            'title' => 'Mountain dusk',
            'alt' => 'Violet mountain ridges against an orange dusk sky',
            'caption' => 'Mountain ridges at dusk.',
        ],
    ];

    public function run(): void
    {
        $storage = Storage::disk('public');

        UserFactory::new()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $date = Carbon::parse('2026-01-05 09:00:00');
        $media = [];

        foreach (self::MEDIA as $name => $details) {
            $source = dirname(__DIR__, 2) . "/fixtures/media/{$name}.jpg";
            $path = "{$name}.jpg";
            [$width, $height] = getimagesize($source);

            $storage->put($path, (string) file_get_contents($source), 'public');

            $media[$name] = Media::query()->forceCreate([
                'disk' => 'public',
                'directory' => null,
                'visibility' => 'public',
                'name' => $name,
                'path' => $path,
                'width' => $width,
                'height' => $height,
                'size' => filesize($source),
                'type' => 'image/jpeg',
                'ext' => 'jpg',
                'alt' => $details['alt'],
                'title' => $details['title'],
                'caption' => $details['caption'],
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $date = $date->copy()->addDay();
        }

        $posts = [
            'Notes from the mountain trail' => ['mountain-dusk', ['mountain-dusk', 'forest-hills', 'northern-lights', 'lake-reflection']],
            'A week on the coast' => ['ocean-horizon', ['ocean-horizon', 'paper-bands', 'desert-dunes']],
            'Crossing the dunes' => ['desert-dunes', ['desert-dunes', 'mountain-dusk']],
            'Chasing the aurora' => ['northern-lights', ['northern-lights', 'geometric-circles', 'lake-reflection', 'forest-hills', 'ocean-horizon']],
            'Shapes and colour studies' => ['geometric-circles', []],
        ];

        $date = Carbon::parse('2026-01-20 09:00:00');

        foreach ($posts as $title => [$featured, $gallery]) {
            $post = Post::query()->forceCreate([
                'title' => $title,
                'content' => '<p>Edit this post to exercise Curator’s picker and rich-editor attachment tool.</p>',
                'featured_image_id' => $media[$featured]->getKey(),
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($gallery as $order => $name) {
                Mediable::query()->forceCreate([
                    'mediable_type' => $post->getMorphClass(),
                    'mediable_id' => $post->getKey(),
                    'media_id' => $media[$name]->getKey(),
                    'order' => $order + 1,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            $date = $date->copy()->addDay();
        }
    }
}
