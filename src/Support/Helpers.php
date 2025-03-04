<?php

namespace Awcodes\Curator\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

class Helpers
{
    public static function getUrl(?string $disk, ?string $path): string
    {
        if (blank($disk) || blank($path)) {
            return '';
        }

        if (
            config('curator.should_check_exists', true)
            && Storage::disk($disk)->exists($path) === false
        ) {
            return '';
        }

        try {
            $isPrivate = config('curator.visibility', 'public') === 'private'
                || Storage::disk($disk)->getVisibility($path) === 'private';
        } catch (Throwable) {
            // ACL not supported on Storage Bucket, Laravel only throws exception here so need to be careful.
            // so we assume it's private
            $isPrivate = config(sprintf('filesystems.disks.%s.visibility', $disk)) !== 'public';
        }

        return $isPrivate
            ? Storage::disk($disk)->temporaryUrl($path, now()->addMinutes(5))
            : Storage::disk($disk)->url($path);
    }
}
