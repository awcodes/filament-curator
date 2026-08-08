<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config\Concerns;

trait SupportsCloudDisks
{
    public function isUsingS3(): bool
    {
        $diskBeforeTestFake = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');

        return config('filesystems.disks.' . mb_strtolower((string) $diskBeforeTestFake) . '.driver') === 's3';
    }

    public function isUsingGCS(): bool
    {
        $diskBeforeTestFake = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');

        return config('filesystems.disks.' . mb_strtolower((string) $diskBeforeTestFake) . '.driver') === 'gcs';
    }

    public function isUsingCloudinary(): bool
    {
        $diskBeforeTestFake = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');

        return config('filesystems.disks.' . mb_strtolower((string) $diskBeforeTestFake) . '.driver') === 'cloudinary';
    }

    public function isUsingCloudDisk(): bool
    {
        if (static::isUsingS3()) {
            return true;
        }
        if (static::isUsingGCS()) {
            return true;
        }

        return static::isUsingCloudinary();
    }
}
