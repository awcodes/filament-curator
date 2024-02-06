<?php

namespace Awcodes\Curator\Database\Factories;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        $fileName = collect([
            'alberto-restifo-Ni4NgA64TFQ-unsplash',
            'blake-verdoorn-cssvEZacHvQ-unsplash',
            'daniel-roe-lpjb_UMOyx8-unsplash',
            'dave-hoefler-lsoogGC_5dg-unsplash',
            'david-marcu-78A265wPiO4-unsplash',
            'dawid-zawila--G3rw6Y02D0-unsplash',
            'eberhard-grossgasteiger-pBgnT4KH8d4-unsplash',
            'eberhard-grossgasteiger-y2azHvupCVo-unsplash',
            'fabian-quintero-UWQP2mh5YJI-unsplash',
            'federico-respini-sYffw0LNr7s-unsplash',
            'felix-mittermeier-L4-16dmZ-1c-unsplash',
            'guillaume-briard-QegnXyECDfw-unsplash',
            'henry-be-IicyiaPYGGI-unsplash',
            'igor-kasalovic-tNDvFkxkBHo-unsplash',
            'joel-vodell-TApAkERW5pQ-unsplash',
            'kees-streefkerk-Adl90-aXYwA-unsplash',
            'luca-bravo-zAjdgNXsMeg-unsplash',
            'lukasz-szmigiel-jFCViYFYcus-unsplash',
            'niko-photos-tGTVxeOr_Rs-unsplash',
            'robert-lukeman-_RBcxo9AU-U-unsplash',
            'robert-lukeman-zNN6ubHmruI-unsplash',
            'tim-swaan-eOpewngf68w-unsplash',
        ])->random() . '.jpg';

        $directory = config('curator.directory');
        $disk = config('curator.disk');

        if (! Storage::disk($disk)->exists($directory . '/' . $fileName)) {
            $fileContents = file_get_contents('https://res.cloudinary.com/aw-codes/image/upload/curator/seed-data/' . $fileName);
            Storage::disk($disk)->put($directory . '/' . $fileName, $fileContents);
        }

        $data = Image::make(Storage::disk($disk)->path($directory . '/' . $fileName));

        return [
            'name' => $data->filename,
            'path' => $directory . '/' . $fileName,
            'ext' => $data->extension,
            'type' => $data->mime(),
            'alt' => $this->faker->words(rand(3, 8), true),
            'title' => null,
            'caption' => null,
            'description' => null,
            'width' => $data->getWidth() ?? null,
            'height' => $data->getHeight() ?? null,
            'disk' => $disk,
            'directory' => $directory,
            'size' => $data->filesize() ?? null,
            'visibility' => 'public',
        ];
    }

    public function private(): MediaFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'visibility' => 'private',
            ];
        });
    }

    public function randomTimestamps(): MediaFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => \Carbon\Carbon::now()->addDays(rand(-800, 0))->addMinutes(rand(
                    0,
                    60 * 23
                ))->addSeconds(rand(0, 60)),
                'updated_at' => \Carbon\Carbon::now()->addDays(rand(-799, 0))->addMinutes(rand(
                    0,
                    60 * 23
                ))->addSeconds(rand(0, 60)),
            ];
        });
    }
}
