<?php

declare(strict_types=1);

namespace Awcodes\Curator\Models;

use Awcodes\Curator\Concerns\HasPackageFactory;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Observers\MediaObserver;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * @property-read int|string $id
 * @property string $disk
 * @property string $directory
 * @property string $visibility
 * @property string $name
 * @property string $path
 * @property int $width
 * @property int $height
 * @property int $size
 * @property string $type
 * @property string $ext
 * @property string|null $alt
 * @property string|null $title
 * @property string|null $description
 * @property string|null $caption
 * @property array|null $exif
 * @property array|null $curations
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read string $url
 * @property-read string $full_path
 * @property-read string $thumbnail_url
 * @property-read string $medium_url
 * @property-read string $large_url
 * @property-read string $pretty_name
 */
#[ObservedBy([MediaObserver::class])]
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
        'thumbnail_url',
        'medium_url',
        'large_url',
        'pretty_name',
    ];

    public function url(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                $storage = Storage::disk($this->disk);

                try {
                    $isPrivate = $storage->getVisibility($this->path) === 'private';
                } catch (Throwable) {
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

    public function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Storage::disk($this->disk)->path($this->path),
        );
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Curator::getUrlProvider()::getThumbnailUrl($this->path),
        );
    }

    public function mediumUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Curator::getUrlProvider()::getMediumUrl($this->path),
        );
    }

    public function largeUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Curator::getUrlProvider()::getLargeUrl($this->path),
        );
    }

    public function prettyName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->getPrettyName()
        );
    }

    public function getPrettyName(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        return $this->name.'.'.$this->ext;
    }

    public function getCuration(string $key): array
    {
        return Arr::first(
            collect($this->curations)->filter(fn (array $item): bool => $item['curation']['key'] === $key)->toArray()
        )['curation'] ?? [];
    }

    public function hasCuration(string $key): bool
    {
        return filled($this->getCuration($key));
    }
}
