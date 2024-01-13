<?php

namespace Awcodes\Curator\Models;

use Awcodes\Curator\Concerns\HasPackageFactory;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\CuratorUtils;
use Awcodes\Curator\Glide\GlideBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use function Awcodes\Curator\is_media_resizable;

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

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function (): string | null {
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

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->is_resizable && $this->isLocal()
                ? GlideBuilder::make()
                    ->width(200)
                    ->height(200)
                    ->fit('crop')
                    ->format('webp')
                    ->toUrl($this->path)
                : $this->url,
        );
    }

    protected function mediumUrl(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->is_resizable && $this->isLocal()
                ? GlideBuilder::make()
                    ->width(640)
                    ->height(640)
                    ->fit('crop')
                    ->format('webp')
                    ->toUrl($this->path)
                : $this->url,
        );
    }

    protected function largeUrl(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->is_resizable && $this->isLocal()
                ? GlideBuilder::make()
                    ->width(1024)
                    ->height(1024)
                    ->fit('contain')
                    ->format('webp')
                    ->toUrl($this->path)
                : $this->url,
        );
    }

    protected function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn(): string => Storage::disk($this->disk)->path($this->path),
        );
    }

    protected function isResizable(): Attribute
    {
        return Attribute::make(
            get: fn(): bool => CuratorUtils::isResizable($this->ext),
        );
    }

    protected function isPreviewable(): Attribute
    {
        return Attribute::make(
            get: fn(): bool => CuratorUtils::isPreviewable($this->ext),
        );
    }

    protected function sizeForHumans(): Attribute
    {
        return Attribute::make(
            get: fn(): string => Number::fileSize(
                bytes: $this->size,
                precision: 2
            )
        );
    }

    protected function prettyName(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->getPrettyName()
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

    public function isLocal(): bool
    {
        return ! CuratorUtils::isUsingCloudDisk();
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
