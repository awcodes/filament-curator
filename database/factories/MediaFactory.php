<?php

namespace Awcodes\Curator\Database\Factories;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use \Illuminate\Http\UploadedFile;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    protected ?string $directory = null;

    protected ?string $disk = null;

    protected ?string $type = null;

    public function definition(): array
    {
       return match ($this->getType()) {
            'svg' => $this->handleSvg(),
            'pdf' => $this->handlePdf(),
            'video' => $this->handleVideo(),
            default => $this->handleJpg(),
        };
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
                'created_at' => \Carbon\Carbon::now()->addDays(rand(-800, 0))->addMinutes(rand(0,
                    60 * 23))->addSeconds(rand(0, 60)),
                'updated_at' => \Carbon\Carbon::now()->addDays(rand(-799, 0))->addMinutes(rand(0,
                    60 * 23))->addSeconds(rand(0, 60))
            ];
        });
    }

    public function directory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDirectory(): ?string
    {
        return $this->directory ?? config('curator.directory');
    }

    public function getDisk(): ?string
    {
        return $this->disk ?? config('curator.disk');
    }

    public function getType(): string
    {
        return $this->type ?? 'jpg';
    }

    public function handleJpg(): array
    {
        $filename = collect([
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

        $disk = $this->getDisk();
        $directory = $this->getDirectory();

        if (!Storage::disk($disk)->exists($directory . '/' . $filename)) {
            $fileContents = file_get_contents('https://res.cloudinary.com/aw-codes/image/upload/curator/seed-data/' . $filename);
            Storage::disk($disk)->put($directory . '/' . $filename, $fileContents);
        }

        $data = Image::make(Storage::disk($disk)->path($directory . '/' . $filename));

        return [
            'name' => $data->filename,
            'path' => $directory ? $directory . '/' . $filename : $filename,
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

    public function handleSvg(): array
    {
        $filename = collect([
            'awcodes-logo',
            'search',
            'screen',
            'brand',
            'apps',
            'link',
        ])->random() . '.svg';

        $disk = $this->getDisk();
        $directory = $this->getDirectory();

        if (!Storage::disk($disk)->exists($directory . '/' . $filename)) {
            $fileContents = file_get_contents('https://res.cloudinary.com/aw-codes/image/upload/curator/seed-data/' . $filename);
            Storage::disk($disk)->put($directory . '/' . $filename, $fileContents);
        }

        return [
            'name' => Str::of($filename)->before('.svg')->toString(),
            'path' => $directory ? $directory . '/' . $filename : $filename,
            'ext' => 'svg',
            'type' => Storage::disk($disk)->mimeType($directory . '/' . $filename),
            'alt' => $this->faker->words(rand(3, 8), true),
            'title' => null,
            'caption' => null,
            'description' => null,
            'width' => null,
            'height' => null,
            'disk' => $disk,
            'directory' => $directory,
            'size' => Storage::disk($disk)->size($directory . '/' . $filename) ?? null,
            'visibility' => 'public',
        ];
    }

    public function handlePdf(): array
    {
        $filename = Str::uuid() . '.pdf';
        $filesize = mt_rand(1000, 2000);
        $disk = $this->getDisk();
        $directory = $this->getDirectory();

        UploadedFile::fake()->create($filename, $filesize, 'application/pdf')->storeAs($directory . '/' . $filename, [
            'disk' => $disk,
        ]);

        return [
            'name' => Str::of($filename)->before('.pdf')->toString(),
            'path' => $directory ? $directory . '/' . $filename : $filename,
            'ext' => 'pdf',
            'type' => 'application/pdf',
            'alt' => $this->faker->words(rand(3, 8), true),
            'title' => null,
            'caption' => null,
            'description' => null,
            'width' => null,
            'height' => null,
            'disk' => $disk,
            'directory' => $directory,
            'size' => $filesize,
            'visibility' => 'public',
        ];
    }

    public function handleVideo(): array
    {
        $filename = Str::uuid() . '.mp4';
        $filesize = mt_rand(1000, 2000);
        $disk = $this->getDisk();
        $directory = $this->getDirectory();

        UploadedFile::fake()->create($filename, $filesize, 'video/mp4')->storeAs($directory . '/' . $filename, [
            'disk' => $disk,
        ]);

        return [
            'name' => Str::of($filename)->before('.pdf')->toString(),
            'path' => $directory ? $directory . '/' . $filename : $filename,
            'ext' => 'mp4',
            'type' => 'video/mp4',
            'alt' => $this->faker->words(rand(3, 8), true),
            'title' => null,
            'caption' => null,
            'description' => null,
            'width' => null,
            'height' => null,
            'disk' => $disk,
            'directory' => $directory,
            'size' => $filesize,
            'visibility' => 'public',
        ];
    }
}
