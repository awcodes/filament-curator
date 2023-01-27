<?php

namespace Awcodes\Curator\Observers;

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use stdClass;

class MediaObserver
{
    /**
     * Handle the Media "creating" event.
     *
     * @param Media $media
     * @return void
     */
    public function creating(Media $media): void
    {
        if ($this->hasMediaUpload($media)) {
            foreach ($media->file as $k => $v) {
                if ($k === 'name') {
                    $media->{$k} = $v->toString();
                } else {
                    $media->{$k} = $v;
                }
            }
        }

        $media->__unset('file');
    }

    /**
     * Handle the Media "created" event.
     *
     * @param Media $media
     * @return void
     */
    public function created(Media $media): void
    {
        //
    }

    /**
     * Handle the Media "updating" event.
     *
     * @param Media $media
     * @return void
     */
    public function updating(Media $media): void
    {
        // Replace image
        if ($this->hasMediaUpload($media)) {
            if (Storage::disk($media->disk)->exists($media->directory.'/'.$media->getOriginal()['name'].'.'.$media->getOriginal()['ext'])) {
                Storage::disk($media->disk)->delete($media->directory.'/'.$media->getOriginal()['name'].'.'.$media->getOriginal()['ext']);
            }

            foreach ($media->file as $k => $v) {
                $media->{$k} = $v;
            }

            Storage::disk($media->disk)->move($media->path, $media->directory.'/'.$media->getOriginal()['name'].'.'.$media->ext);

            $media->name = $media->getOriginal()['name'];
            $media->path = $media->directory.'/'.$media->getOriginal()['name'].'.'.$media->ext;
        }

        // Rename file name
        if ($media->isDirty(['name']) && ! blank($media->name)) {
            if (Storage::disk($media->disk)->exists($media->directory.'/'.$media->name.'.'.$media->ext)) {
                $media->name = $media->name.'-'.time();
            }
            Storage::disk($media->disk)->move($media->path, $media->directory.'/'.$media->name.'.'.$media->ext);
            $media->path = $media->directory.'/'.$media->name.'.'.$media->ext;
        }

        $media->__unset('file');
    }

    /**
     * Handle the Media "updated" event.
     *
     * @param Media $media
     * @return void
     */
    public function updated(Media $media): void
    {
        //
    }

    /**
     * Handle the Media "deleted" event.
     *
     * @param  Media  $media
     * @return void
     */
    public function deleted(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->path);

        if (Storage::disk($media->disk)->allFiles($media->directory . '/' . $media->name)) {
            Storage::disk($media->disk)->deleteDirectory($media->directory . '/' . $media->name);
        }

        if (count(Storage::disk($media->disk)->allFiles($media->directory)) == 0) {
            Storage::disk($media->disk)->deleteDirectory($media->directory);
        }
    }

    /**
     * Handle the Media "restored" event.
     *
     * @param  Media  $media
     * @return void
     */
    public function restored(Media $media): void
    {
        //
    }

    /**
     * Handle the Media "forceDeleted" event.
     *
     * @param Media $media
     * @return void
     */
    public function forceDeleted(Media $media): void
    {
        //
    }

    private function hasMediaUpload($media): bool
    {
        return is_array($media->file) || $media->file instanceof stdClass;
    }
}
