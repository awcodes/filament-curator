<?php

namespace Awcodes\Curator\Observers;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use stdClass;

class MediaObserver
{
    /**
     * Handle the Media "creating" event.
     */
    public function creating(Media $media): void
    {
        if ($this->hasMediaUpload($media)) {
            foreach ($media->file as $k => $v) {
                if ($k === 'name') {
                    if (is_string($v)) {
                        $media->{$k} = $v;
                    } else {
                        $media->{$k} = $v->toString();
                    }
                } elseif ($k === 'exif' && is_array($v)) {
                    $media->{$k} = Curator::sanitizeExif($v);
                } else {
                    $media->{$k} = $v;
                }
            }
        }

        $media->__unset('file');
    }

    /**
     * Handle the Media "updating" event.
     */
    public function updating(Media $media): void
    {
        $storage = Storage::disk($media->disk);

        // Replace image
        if ($this->hasMediaUpload($media)) {
            if ($storage->exists($media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->getOriginal()['ext'])) {
                $storage->delete($media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->getOriginal()['ext']);
            }

            foreach ($media->file as $k => $v) {
                $media->{$k} = $v;
            }

            $storage->move($media->path, $media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->ext);

            $media->name = $media->getOriginal()['name'];
            $media->path = $media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->ext;

            // Delete glide-cache for replaced image
            $server = Glide::getServer();
            $server->deleteCache($media->path);
        }

        // Rename file name
        if ($media->isDirty(['name']) && ! blank($media->name)) {
            if ($storage->exists($media->directory . '/' . $media->name . '.' . $media->ext)) {
                $media->name = $media->name . '-' . time();
            }
            $storage->move($media->path, $media->directory . '/' . $media->name . '.' . $media->ext);
            $media->path = $media->directory . '/' . $media->name . '.' . $media->ext;
        }

        $media->__unset('file');
        $media->__unset('originalFilename');
    }

    /**
     * Handle the Media "deleted" event.
     */
    public function deleted(Media $media): void
    {
        $storage = Storage::disk($media->disk);

        $storage->delete($media->path);

        if ($storage->allFiles($media->directory . '/' . $media->name)) {
            $storage->deleteDirectory($media->directory . '/' . $media->name);
        }

        if (count($storage->allFiles($media->directory)) == 0) {
            $storage->deleteDirectory($media->directory);
        }

        // Delete glide-cache for delete image
        $server = Glide::getServer();
        $server->deleteCache($media->path);
    }

    private function hasMediaUpload($media): bool
    {
        return is_array($media->file) || $media->file instanceof stdClass;
    }
}
