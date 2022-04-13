<?php

namespace FilamentCurator\Observers;

use FilamentCurator\Models\Media;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class MediaObserver
{
    /**
     * Handle the Media "creating" event.
     *
     * @param  \FilamentCurator\Models\Media  $media
     * @return void
     */
    public function creating(Media $media)
    {
        foreach ($media->filename as $k => $v) {
            $media->{$k} = $v;
        }
    }

    /**
     * Handle the Media "created" event.
     *
     * @param  \FilamentCurator\Models\Media  $media
     * @return void
     */
    public function created(Media $media)
    {
        $media->refresh();
        self::generateThumbs($media);

        if ($media->disk == 'cloudinary') {
            $file = Storage::disk($this->defineDisk($media->disk))->get($media->directory . '/' . $media->filename);
            $uploadedFile = $file->storeOnCloudinaryAs(config('filament-curator.directory'), $media->filename);

            $media->update([
                'public_id' => $uploadedFile->getPublicId(),
                'type' => $uploadedFile->getFileType(),
            ]);
        }
    }

    /**
     * Handle the Media "deleted" event.
     *
     * @param  \FilamentCurator\Models\Media  $media
     * @return void
     */
    public function deleted(Media $media)
    {
        $pathinfo = pathinfo($media->filename);
        if ($media->disk !== 'cloudinary') {
            Storage::disk($media->disk)->delete($media->filename);
            foreach (config('filament-curator.sizes') as $name => $data) {
                Storage::disk($media->disk)->delete($pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $name . '.' . $media->ext);
            }
        }
    }

    public static function defineDisk($disk): string
    {
        return $disk == 'cloudinary' ? 'public' : $disk;
    }

    public static function generateThumbs(Media $media): void
    {
        $pathinfo = pathinfo($media->filename);
        foreach (config('filament-curator.sizes') as $name => $mediaSize) {
            $image = Image::make(Storage::disk(self::defineDisk($media->disk))->path($media->filename));

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
            Storage::disk(self::defineDisk($media->disk))->put($pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $name . '.' . $media->ext, $image);
        }
    }
}
