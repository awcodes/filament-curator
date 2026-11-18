<?php

declare(strict_types=1);

namespace Awcodes\Curator\Observers;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
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
                    $media->{$k} = is_string($v) ? $v : $v->toString();
                } elseif ($k === 'exif' && is_array($v)) {
                    $media->{$k} = Curator::sanitizeExif($v);
                } else {
                    $media->{$k} = $v;
                }
            }
        }

        $this->assignTenant($media);
        $this->keepOwnCurations($media);

        $media->__unset('file');
    }

    /**
     * Handle the Media "updating" event.
     */
    public function updating(Media $media): void
    {
        $this->keepOwnCurations($media);

        // Replace image
        if ($this->hasMediaUpload($media)) {
            $this->swapFile($media);
        }

        // Rename file name
        if ($media->isDirty(['name']) && ! blank($media->name)) {
            $storage = Storage::disk($media->disk);
            $originalName = $media->getOriginal('name');
            $originalPath = $media->path;

            $media->name = Media::uniqueName($media->disk, $media->directory, $media->name, $media->ext, $media->getKey());

            $renamedPath = $this->pathIn($media->directory, $media->name . '.' . $media->ext);

            // A record whose file is already missing can still be renamed; there is just nothing to move.
            if ($storage->exists($originalPath) && ! $storage->move($originalPath, $renamedPath)) {
                throw new RuntimeException("Unable to rename [{$originalPath}] to [{$renamedPath}].");
            }

            $media->path = $renamedPath;

            if (filled($originalName)) {
                $this->moveCurations($media, $originalName, $media->name);
            }

            Glide::getServer($media->disk)->deleteCache($originalPath);
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

        $this->deleteCurations($media);

        // A blank directory is the disk root, which is never ours to remove.
        if (filled($media->directory) && count($storage->allFiles($media->directory)) === 0) {
            $storage->deleteDirectory($media->directory);
        }

        // Delete glide-cache for delete image
        Glide::getServer($media->disk)->deleteCache($media->path);
    }

    /**
     * Moves the uploaded replacement to the original's name, keeping its new extension. The original is removed
     * only once the replacement is in place, so a failed move never loses the file being replaced.
     */
    protected function swapFile(Media $media): void
    {
        $originalDisk = $media->getOriginal('disk');
        $originalPath = $media->getOriginal('path');
        $originalName = $media->getOriginal('name');

        foreach ($media->file as $k => $v) {
            $media->{$k} = $v;
        }

        $replacedPath = $this->pathIn($media->directory, $originalName . '.' . $media->ext);

        // The upload is on its own disk, which may not be the original's.
        if (! $this->isSamePath($media->path, $replacedPath) && ! Storage::disk($media->disk)->move($media->path, $replacedPath)) {
            throw new RuntimeException("Unable to move the replacement file [{$media->path}] into place.");
        }

        // Older rows can store the same path with a leading slash, so compare them as the disk resolves them.
        if ($originalDisk !== $media->disk || ! $this->isSamePath($originalPath, $replacedPath)) {
            Storage::disk($originalDisk)->delete($originalPath);
        }

        $media->name = $originalName;
        $media->path = $replacedPath;

        Glide::getServer($originalDisk)->deleteCache($originalPath);
        Glide::getServer($media->disk)->deleteCache($replacedPath);
    }

    /**
     * Curations are stored in a folder named after the media item. Renaming the item moves them with it and updates
     * the stored paths, so they keep resolving and are found again when the item is deleted.
     */
    protected function moveCurations(Media $media, string $fromName, string $toName): void
    {
        if (blank($media->curations)) {
            return;
        }

        $storage = Storage::disk($media->disk);
        $fromFolder = $this->pathIn($media->directory, $fromName);
        $toFolder = $this->pathIn($media->directory, $toName);

        $curations = [];

        foreach ($media->curations as $item) {
            $path = $item['curation']['path'] ?? null;

            if (! Media::isCurationFileIn($item['curation']['disk'] ?? null, $path, $media->disk, $media->directory, $fromName) || $media->isAnotherMediaFile($path)) {
                $curations[] = $item;

                continue;
            }

            $newPath = $toFolder . '/' . basename($path);

            if ($storage->exists($path) && ! $storage->move($path, $newPath)) {
                throw new RuntimeException("Unable to move the curation [{$path}] to [{$newPath}].");
            }

            $item['curation']['path'] = $newPath;
            $item['curation']['directory'] = $toName;
            $item['curation']['url'] = Media::resolveUrl($media->disk, $newPath, $item['curation']['visibility'] ?? $media->visibility);

            $curations[] = $item;
        }

        $media->curations = $curations;

        $this->deleteFolderIfEmpty($media->disk, $fromFolder);
    }

    /**
     * Curations arrive from form state. An entry naming a file inside the item's own curation folder that isn't
     * another item's is kept, with its disk, visibility and URL set here. Any other entry is kept only when it is
     * already stored on the item, and then exactly as stored, so the delete, move and URL steps never act on any other
     * file.
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

            if (Media::isCurationFileIn($curation['disk'] ?? null, $curation['path'], $disk, $directory, $name) && ! $media->isAnotherMediaFile($curation['path'])) {
                $curation['disk'] = $disk;
                $curation['visibility'] = $visibility;
                $curation['url'] = Media::resolveUrl($disk, $curation['path'], $visibility);
                $kept[$index] = [...$item, 'curation' => $curation];

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
     * Deletes only the files the media's curations point to, then their folder if that leaves it empty. A folder that
     * merely shares the item's name, such as `uploads/` beside `uploads.jpg`, is never touched.
     */
    protected function deleteCurations(Media $media): void
    {
        if (blank($media->curations)) {
            return;
        }

        $storage = Storage::disk($media->disk);
        $folders = [];

        foreach ($media->curations as $item) {
            $path = $item['curation']['path'] ?? null;

            if (! $media->ownsCurationFile($item['curation']['disk'] ?? null, $path)) {
                continue;
            }

            $storage->delete($path);
            $folders[] = dirname(ltrim($path, '/'));
        }

        foreach (array_unique($folders) as $folder) {
            if ($folder !== '.' && $folder !== rtrim((string) $media->directory, '/')) {
                $this->deleteFolderIfEmpty($media->disk, $folder);
            }
        }
    }

    /**
     * Media created through the picker's panel or the multi upload action is
     * mass assigned, and the tenant key is not fillable, so it is set here.
     * When the media resource is registered on the tenant panel, Filament has
     * already associated the tenant and this leaves it alone.
     */
    protected function assignTenant(Media $media): void
    {
        if (! Curator::isTenantAware() || ! Filament::hasTenancy() || ! ($tenant = Filament::getTenant()) instanceof Model) {
            return;
        }

        $relationship = Curator::getTenantName() ?? Filament::getTenantOwnershipRelationshipName();
        $foreignKey = $relationship . '_id';

        if (blank($media->getAttribute($foreignKey))) {
            $media->setAttribute($foreignKey, $tenant->getKey());
        }
    }

    protected function deleteFolderIfEmpty(string $disk, string $folder): void
    {
        $storage = Storage::disk($disk);

        if ($storage->directoryExists($folder) && $storage->allFiles($folder) === [] && $storage->allDirectories($folder) === []) {
            $storage->deleteDirectory($folder);
        }
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

    /**
     * Media stored at the disk root has a null directory, which must not leave
     * a leading slash in the stored path: lookups by path would miss it.
     */
    private function pathIn(?string $directory, string $file): string
    {
        return filled($directory) ? rtrim($directory, '/') . '/' . $file : $file;
    }

    private function isSamePath(string $first, string $second): bool
    {
        return ltrim($first, '/') === ltrim($second, '/');
    }

    private function hasMediaUpload(Media $media): bool
    {
        if (! isset($media->file)) {
            return false;
        }

        return is_array($media->file) || $media->file instanceof stdClass;
    }
}
