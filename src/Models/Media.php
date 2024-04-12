<?php

namespace Awcodes\Curator\Models;

use Awcodes\Curator\Concerns\HasPackageFactory;
use Awcodes\Curator\Config\GlideManager;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasPackageFactory;

    protected $table = 'curator';

    protected $fillable = [
        'disk',
        'directory',
        'visibility',
        'name',
        'path',
        'width',
        'height',
        'size',
        'type',
        'ext',
        'alt',
        'title',
        'description',
        'caption',
        'exif',
        'curations',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'integer',
        'curations' => 'array',
        'exif' => 'array',
    ];

    protected $appends = [
        'url',
        'full_path',
        'pretty_name',
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                $storage = Storage::disk($this->disk);

                try {
                    $isPrivate = $storage->getVisibility($this->path) === 'private';
                } catch (\Throwable) {
                    // ACL not supported on Storage Bucket, Laravel only throws exception here so need to be careful.
                    // so we assume it's private
                    $isPrivate = true;
                }

                return $isPrivate
                    ? $storage->temporaryUrl($this->path, now()->addMinutes(5))
                    : $storage->url($this->path);
            },
        );
    }

    protected function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Storage::disk($this->disk)->path($this->path),
        );
    }

    protected function prettyName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->getPrettyName()
        );
    }

    public function getGlideUrl(array $params = []): string
    {
        return app(GlideManager::class)->getUrl($this->path, $params);
    }

    public function getPrettyName(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        return $this->name . '.' . $this->ext;
    }

    public function getCuration(string $key): array
    {
        return Arr::first(collect($this->curations)->filter(function ($item) use ($key) {
            return $item['curation']['key'] === $key;
        }))['curation'] ?? [];
    }

    public function hasCuration(string $key): bool
    {
        return filled($this->getCuration($key));
    }
}
