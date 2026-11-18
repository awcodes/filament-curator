<?php

declare(strict_types=1);

namespace Awcodes\Curator\Observers;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Models\Media;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
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

        $storage = Storage::disk($media->disk);

        // Replace image
        if ($this->hasMediaUpload($media)) {
            if ($storage->exists($media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->getOriginal()['ext'])) {
                $storage->delete($media->directory . '/' . $media->getOriginal()['name'] . '.' . $media->getOriginal()['ext']);
            }

            foreach ($media->file as $k => $v) {
                $media->{$k} = $v;
            }

            $originalName = $media->getOriginal('name');

            // The replacement takes the original's place, so the item never moves into a folder where its name may
            // belong to another item. On another disk it lands in the upload's folder, under a name free there.
            if ($media->disk === $media->getOriginal('disk')) {
                $media->directory = $media->getOriginal('directory');
            } else {
                $originalName = Media::uniqueName($media->disk, $media->directory, $originalName, $media->ext, $media->getKey());
            }

            Storage::disk($media->disk)->move($media->path, $media->directory . '/' . $originalName . '.' . $media->ext);

            $media->name = $originalName;
            $media->path = $media->directory . '/' . $originalName . '.' . $media->ext;

            // Delete glide-cache for replaced image
            $server = Glide::getServer();
            $server->deleteCache($media->path);
        }

        // Rename file name
        if ($media->isDirty(['name']) && ! blank($media->name)) {
            $media->name = Media::uniqueName($media->disk, $media->directory, $media->name, $media->ext, $media->getKey());

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

        // The curation folder is named after the item, so another item's files can live in a folder of that name.
        $curationFolder = Media::curationFolder($media->directory, $media->name);

        if ($storage->allFiles($curationFolder) && ! $media->hasAnotherMediaFileIn($curationFolder)) {
            $storage->deleteDirectory($curationFolder);
        }

        if (count($storage->allFiles($media->directory)) === 0) {
            $storage->deleteDirectory($media->directory);
        }

        // Delete glide-cache for delete image
        $server = Glide::getServer();
        $server->deleteCache($media->path);
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

    /**
     * Curations arrive from form state. An entry naming a file inside the item's own curation folder that isn't
     * another item's is kept, with its disk, visibility and URL set here. Any other entry is kept only when it is
     * already stored on the item, and then exactly as stored, so the URL steps never act on any other file.
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

    private function hasMediaUpload(Media $media): bool
    {
        if (! isset($media->file)) {
            return false;
        }

        return is_array($media->file) || $media->file instanceof stdClass;
    }
}
