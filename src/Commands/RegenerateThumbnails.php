<?php

namespace FilamentCurator\Commands;

use FilamentCurator\Models\Media;
use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class RegenerateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'curator:regenerate-thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate thumbnails for the site.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $media = Media::all();

        $this->info('Regenerating...');

        $media->map(function ($item) {
            $pathinfo = pathinfo($item->filename);

            foreach (config('filament-curator.sizes') as $name => $mediaSize) {
                /**
                 * Delete existing sizes
                 */
                Storage::disk($item->disk)->delete($pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $name . '.' . $item->ext);

                /**
                 * Generate new sizes
                 */
                $image = Image::make(Storage::disk($this->defineDisk($item->disk))->path($item->filename));

                if ($mediaSize['width'] == $mediaSize['height']) {
                    $image->fit($mediaSize['width']);
                } else {
                    $image->resize($mediaSize['width'], $mediaSize['height'], function ($constraint) use ($mediaSize) {
                        if (!$mediaSize['height']) {
                            $constraint->aspectRatio();
                        }
                    });
                }

                $image->encode(null, $mediaSize['quality']);
                Storage::disk($this->defineDisk($item->disk))->put($pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $name . '.' . $item->ext, $image);
            }

            $this->info("Regenerated {$item->filename} thumbnails.");
        });

        return 0;
    }

    public function defineDisk($disk): string
    {
        return $disk == 'cloudinary' ? 'public' : $disk;
    }
}
