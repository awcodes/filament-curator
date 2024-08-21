<?php

namespace Awcodes\Curator\Observers;

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
                    // Fix malformed utf-8 characters
                    array_walk_recursive($v, function (&$entry) {
                        if (! mb_detect_encoding($entry, 'utf-8', true)) {
                            $entry = mb_convert_encoding($entry, 'utf-8');
                        }
                    });

                    $media->{$k} = $v;
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
        // Replace image
        if ($this->hasMediaUpload($media)) {
            if (Storage::disk($media->disk)->exists($media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->getOriginal()['ext'])) {
                Storage::disk($media->disk)->delete($media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->getOriginal()['ext']);
            }

            foreach ($media->file as $k => $v) {
                $media->{$k} = $v;
            }

            Storage::disk($media->disk)->move($media->path, $media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->ext);

            $media->name = $media->getOriginal()['name'];
            $media->path = $media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->ext;

            // Delete glide-cache for replaced image
            $server = app(config('curator.glide.server'))->getFactory();
            $server->deleteCache($media->path);
        }

        // Rename file name
        if ($media->isDirty(['name']) && ! blank($media->name)) {
            if (Storage::disk($media->disk)->exists($media->directory . '/' . $media->name . '.' . $media->ext)) {
                $media->name = $media->name . '-' . time();
            }
            Storage::disk($media->disk)->move($media->path, $media->directory . '/' . $media->name . '.' . $media->ext);
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
        Storage::disk($media->disk)->delete($media->path);

        if (Storage::disk($media->disk)->allFiles($media->directory . '/' . $media->name)) {
            Storage::disk($media->disk)->deleteDirectory($media->directory . '/' . $media->name);
        }

        if (count(Storage::disk($media->disk)->allFiles($media->directory)) == 0) {
            Storage::disk($media->disk)->deleteDirectory($media->directory);
        }

        // Delete glide-cache for delete image
        $server = app(config('curator.glide.server'))->getFactory();
        $server->deleteCache($media->path);
    }

    private function hasMediaUpload($media): bool
    {
        return is_array($media->file) || $media->file instanceof stdClass;
    }
}
