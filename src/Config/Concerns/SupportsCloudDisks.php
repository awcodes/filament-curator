<?php

namespace Awcodes\Curator\Config\Concerns;

trait SupportsCloudDisks
{
    public function isUsingS3(): bool
    {
        $diskBeforeTestFake = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');

        return config('filesystems.disks.'.strtolower($diskBeforeTestFake).'.driver') === 's3';
    }

    public function isUsingGCS(): bool
    {
        $diskBeforeTestFake = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');

        return config('filesystems.disks.'.strtolower($diskBeforeTestFake).'.driver') === 'gcs';
    }

    public function isUsingCloudinary(): bool
    {
        $diskBeforeTestFake = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');

        return config('filesystems.disks.'.strtolower($diskBeforeTestFake).'.driver') === 'cloudinary';
    }

    public function isUsingCloudDisk(): bool
    {
        return static::isUsingS3() || static::isUsingGCS() || static::isUsingCloudinary();
    }
}