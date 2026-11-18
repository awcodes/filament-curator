<?php

namespace Awcodes\Curator\Observers;

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
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
                } else {
                    $media->{$k} = $v;
                }
            }
        }

        $this->keepOwnCurations($media);

        $media->__unset('file');
    }

    /**
     * Handle the Media "updating" event.
     */
    public function updating(Media $media): void
    {
        $this->keepOwnCurations($media);

        $swapped = false;

        // Replace image
        if ($this->hasMediaUpload($media)) {
            $swapped = true;
            $originalDisk = $media->getOriginal('disk');
            $originalPath = $media->getOriginal('path');
            $originalName = $media->getOriginal('name');

            foreach ($media->file as $k => $v) {
                $media->{$k} = $v;
            }

            // The replacement takes the original's place, so the item never moves into a folder where its name may
            // belong to another item. On another disk it lands in the upload's folder, under a name free there.
            if ($media->disk === $originalDisk) {
                $media->directory = $media->getOriginal('directory');
            } else {
                $originalName = $media::uniqueName($media->disk, $media->directory, $originalName, $media->ext, $media->getKey());
            }

            $replacedPath = $media->directory . '/' . $originalName . '.' . $media->ext;

            // The original is removed only once the replacement is in place, so a failed move never loses it.
            if (! $this->isSamePath($media->path, $replacedPath) && ! Storage::disk($media->disk)->move($media->path, $replacedPath)) {
                throw new RuntimeException("Unable to move the replacement file [{$media->path}] into place.");
            }

            if ($originalDisk !== $media->disk || ! $this->isSamePath($originalPath, $replacedPath)) {
                Storage::disk($originalDisk)->delete($originalPath);
            }

            $media->name = $originalName;
            $media->path = $replacedPath;

            // Delete glide-cache for replaced image
            $server = app(config('curator.glide.server'))->getFactory();
            $server->deleteCache($media->path);
        }

        // Rename file name. A swap has already placed the file under its final name.
        if (! $swapped && $media->isDirty(['name']) && ! blank($media->name)) {
            $media->name = $media::uniqueName($media->disk, $media->directory, $media->name, $media->ext, $media->getKey());

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

        // The curation folder is named after the item, so another item's files can live in a folder of that name, and
        // older rows can share the item's name, and so its folder, with another extension.
        $curationFolder = Media::curationFolder($media->directory, $media->name);

        if (
            Storage::disk($media->disk)->allFiles($curationFolder)
            && ! $media->hasAnotherMediaFileIn($curationFolder)
            && ! $media::isNameUsedByAnotherMedia($media->disk, $media->directory, $media->name, $media->getKey())
        ) {
            $this->deleteCurationFiles($media->disk, $curationFolder);
        }

        if (count(Storage::disk($media->disk)->allFiles($media->directory)) == 0) {
            Storage::disk($media->disk)->deleteDirectory($media->directory);
        }

        // Delete glide-cache for delete image
        $server = app(config('curator.glide.server'))->getFactory();
        $server->deleteCache($media->path);
    }

    /**
     * Only curation images directly in the folder are removed, since an app may keep its own files in a folder that
     * shares an item's name. The folder goes only when that leaves it empty.
     */
    protected function deleteCurationFiles(string $disk, string $folder): void
    {
        $storage = Storage::disk($disk);

        foreach ($storage->files($folder) as $file) {
            if (Media::isCurationExtension(pathinfo($file, PATHINFO_EXTENSION))) {
                $storage->delete($file);
            }
        }

        if (count($storage->allFiles($folder)) === 0) {
            $storage->deleteDirectory($folder);
        }
    }

    /**
     * Curations arrive from form state. An entry naming a file inside the item's own curation folder that isn't
     * another item's is kept, with its disk, visibility and URL set here. Any other entry is kept only when it is
     * already stored on the item, and then exactly as stored, so a URL is never built for any other file.
     */
    protected function keepOwnCurations(Media $media): void
    {
        $curations = $media->curations;

        if (! is_array($curations) || ($media->exists && ! $media->isDirty('curations'))) {
            return;
        }

        $disk = $media->exists ? $media->getOriginal('disk') : $media->disk;
        $directory = $media->exists ? $media->getOriginal('directory') : $media->directory;
        $name = $media->exists ? $media->getOriginal('name') : $media->name;
        $visibility = $media->exists ? $media->getOriginal('visibility') : $media->visibility;
        $stored = $media->exists ? $this->storedCurations($media->getOriginal('curations'), $disk) : [];

        $kept = [];

        foreach ($curations as $index => $item) {
            $curation = is_array($item) ? ($item['curation'] ?? null) : null;

            if (! is_array($curation) || ! is_string($curation['path'] ?? null)) {
                continue;
            }

            if ($media::isCurationFileIn($curation['disk'] ?? null, $curation['path'], $disk, $directory, $name) && ! $media->isAnotherMediaFile($curation['path'])) {
                $curation['disk'] = $disk;
                $curation['visibility'] = $visibility;
                $curation['url'] = Storage::disk($disk)->url($curation['path']);
                $kept[$index] = array_merge($item, ['curation' => $curation]);

                continue;
            }

            $location = ($curation['disk'] ?? $disk) . "\0" . ltrim($curation['path'], '/');

            if (array_key_exists($location, $stored)) {
                $kept[$index] = $stored[$location];
            }
        }

        $media->curations = array_is_list($curations) ? array_values($kept) : $kept;
    }

    /**
     * @return array<string, mixed>
     */
    private function storedCurations(mixed $curations, ?string $disk): array
    {
        $stored = [];

        foreach (is_array($curations) ? $curations : [] as $item) {
            $curation = is_array($item) ? ($item['curation'] ?? null) : null;

            if (is_array($curation) && is_string($curation['path'] ?? null)) {
                $stored[($curation['disk'] ?? $disk) . "\0" . ltrim($curation['path'], '/')] = $item;
            }
        }

        return $stored;
    }

    private function isSamePath(?string $first, ?string $second): bool
    {
        return ltrim((string) $first, '/') === ltrim((string) $second, '/');
    }

    private function hasMediaUpload($media): bool
    {
        return is_array($media->file) || $media->file instanceof stdClass;
    }
}
