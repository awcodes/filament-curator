<?php

declare(strict_types=1);

namespace Awcodes\Curator\Models;

use Awcodes\Curator\Concerns\HasPackageFactory;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Observers\MediaObserver;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * @property-read int|string $id
 * @property string $disk
 * @property string|null $directory
 * @property string $visibility
 * @property string $name
 * @property string $path
 * @property int|null $width
 * @property int|null $height
 * @property int|null $size
 * @property string $type
 * @property string $ext
 * @property string|null $alt
 * @property string|null $title
 * @property string|null $description
 * @property string|null $caption
 * @property array|null $exif
 * @property array|null $curations
 * @property-read string $placeholder
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read string|null $url
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
        'file',
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

    /**
     * Resolve a publicly accessible URL for a file on the given disk, transparently
     * handling local and remote (e.g. S3) drivers as well as private visibility.
     *
     * Visibility is taken from the stored value (or the configured default) rather
     * than queried from the driver, so this never makes a network round-trip on
     * remote disks such as S3 — important because it runs once per media item
     * whenever a collection of media is serialised (e.g. the CuratorPicker form).
     */
    public static function resolveUrl(string $disk, string $path, ?string $visibility = null): ?string
    {
        $storage = Storage::disk($disk);

        if (blank($visibility)) {
            $visibility = config('curator.default_visibility', 'public');
        }

        if ($visibility === 'private') {
            try {
                return $storage->temporaryUrl($path, now()->addMinutes(5));
            } catch (Throwable) {
                // Driver doesn't support temporary URLs, fall back to regular URL
            }
        }

        return $storage->url($path);
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => static::resolveUrl($this->disk, $this->path, $this->visibility),
        )->shouldCache();
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

    public function placeholder(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $storage = Storage::disk($this->disk);
                $key = 'placeholder:' . $this->name . $storage->lastModified($this->path);

                return Cache::rememberForever($key, function () use ($storage): string {
                    $manager = Glide::getServer()->getApi()->getImageManager();
                    $image = $manager->read($storage->get($this->path));
                    $placeholder = $image->scaleDown(400)->blur(10)->toJpeg(30)->toString();

                    return 'data:image/jpeg;base64,' . base64_encode($placeholder);
                });
            }
        );
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
        return Arr::first(
            collect($this->curations)->filter(fn (array $item): bool => $item['curation']['key'] === $key)->toArray()
        )['curation'] ?? [];
    }

    public function hasCuration(string $key): bool
    {
        return filled($this->getCuration($key));
    }
}
