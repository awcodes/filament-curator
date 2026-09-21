<?php

declare(strict_types=1);

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
                    $media->{$k} = is_string($v) ? $v : $v->toString();
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
            $originalPath = $this->pathIn($media->directory, $media->getOriginal()['name'] . '.' . $media->getOriginal()['ext']);

            if ($storage->exists($originalPath)) {
                $storage->delete($originalPath);
            }

            foreach ($media->file as $k => $v) {
                $media->{$k} = $v;
            }

            $replacedPath = $this->pathIn($media->directory, $media->getOriginal()['name'] . '.' . $media->ext);

            $storage->move($media->path, $replacedPath);

            $media->name = $media->getOriginal()['name'];
            $media->path = $replacedPath;

            // Delete glide-cache for replaced image
            $server = Glide::getServer();
            $server->deleteCache($media->path);
        }

        // Rename file name
        if ($media->isDirty(['name']) && ! blank($media->name)) {
            if ($storage->exists($this->pathIn($media->directory, $media->name . '.' . $media->ext))) {
                $media->name = $media->name . '-' . time();
            }

            $renamedPath = $this->pathIn($media->directory, $media->name . '.' . $media->ext);

            $storage->move($media->path, $renamedPath);
            $media->path = $renamedPath;
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

        // Curations live in a folder named after the media item. Without a
        // name that path collapses to the directory itself (or the disk root).
        if (filled($media->name)) {
            $curations = $this->pathIn($media->directory, $media->name);

            if ($storage->allFiles($curations)) {
                $storage->deleteDirectory($curations);
            }
        }

        // A blank directory is the disk root, which is never ours to remove.
        if (filled($media->directory) && count($storage->allFiles($media->directory)) === 0) {
            $storage->deleteDirectory($media->directory);
        }

        // Delete glide-cache for delete image
        $server = Glide::getServer();
        $server->deleteCache($media->path);
    }

    /**
     * Media stored at the disk root has a null directory, which must not leave
     * a leading slash in the stored path: lookups by path would miss it.
     */
    private function pathIn(?string $directory, string $file): string
    {
        return filled($directory) ? rtrim($directory, '/') . '/' . $file : $file;
    }

    private function hasMediaUpload(Media $media): bool
    {
        if (! isset($media->file)) {
            return false;
        }

        return is_array($media->file) || $media->file instanceof stdClass;
    }
}
