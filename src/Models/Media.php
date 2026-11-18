<?php

declare(strict_types=1);

namespace Awcodes\Curator\Models;

use Awcodes\Curator\Concerns\HasPackageFactory;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Facades\Glide;
use Awcodes\Curator\Observers\MediaObserver;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
 * @property-read string $placeholder
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

    /**
     * Extensions a curation can be saved as, current and past, which bounds what a stored curation can point at.
     */
    protected const CURATION_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'bmp'];

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
                return $storage->temporaryUrl($path, Glide::getTemporaryUrlExpiration());
            } catch (Throwable) {
                // Driver doesn't support temporary URLs, fall back to regular URL
            }
        }

        return $storage->url($path);
    }

    /**
     * A blank visibility falls back to the configured default, as resolveUrl() does.
     */
    public static function isPublicVisibility(?string $visibility): bool
    {
        if (blank($visibility)) {
            $visibility = config('curator.default_visibility', 'public');
        }

        return $visibility === 'public';
    }

    public static function isCurationFileIn(mixed $disk, mixed $path, ?string $mediaDisk, ?string $directory, ?string $name): bool
    {
        $disk ??= $mediaDisk;

        if (! is_string($disk) || ! is_string($path) || blank($mediaDisk) || blank($name) || $disk !== $mediaDisk) {
            return false;
        }

        $folder = static::curationFolder($directory, $name);
        $path = ltrim($path, '/');

        if (! static::isPlainRelativePath($folder) || ! str_starts_with($path, $folder . '/')) {
            return false;
        }

        $file = substr($path, strlen($folder) + 1);

        return $file !== '' && ! str_contains($file, '/') && static::isPlainRelativePath($file);
    }

    public static function curationFolder(?string $directory, string $name): string
    {
        return trim((string) preg_replace('#/+#', '/', filled($directory) ? $directory . '/' . $name : $name), '/');
    }

    /**
     * A name is taken when its file or a folder of that name already exists, or another item on the same disk and in
     * the same directory has it, since an item's curations live in a folder named after it.
     */
    public static function isNameTaken(string $disk, ?string $directory, string $name, ?string $ext = null, mixed $exceptKey = null): bool
    {
        $storage = Storage::disk($disk);
        $base = static::curationFolder($directory, $name);

        if ((filled($ext) && $storage->exists($base . '.' . $ext)) || $storage->directoryExists($base)) {
            return true;
        }

        $directory = static::curationFolder(null, (string) $directory);

        return App::make(static::class)->newQueryWithoutScopes()
            ->where('disk', $disk)
            ->where(fn (Builder $query): Builder => $directory === ''
                ? $query->whereNull('directory')->orWhereIn('directory', ['', '/', '//'])
                : $query->whereIn('directory', [$directory, $directory . '/', '/' . $directory, '/' . $directory . '/']))
            ->where(DB::raw('LOWER(name)'), mb_strtolower($name))
            ->when($exceptKey !== null, fn (Builder $query): Builder => $query->whereKeyNot($exceptKey))
            ->exists();
    }

    public static function uniqueName(string $disk, ?string $directory, string $name, ?string $ext = null, mixed $exceptKey = null): string
    {
        if (! static::isNameTaken($disk, $directory, $name, $ext, $exceptKey)) {
            return $name;
        }

        $suffix = (string) time();
        $candidate = $name . '-' . $suffix;

        for ($attempt = 2; static::isNameTaken($disk, $directory, $candidate, $ext, $exceptKey); $attempt++) {
            $candidate = $name . '-' . $suffix . '-' . $attempt;
        }

        return $candidate;
    }

    public function isPublic(): bool
    {
        return static::isPublicVisibility($this->visibility);
    }

    /**
     * A Glide URL for this media, temporary when it isn't public.
     */
    public function getGlideUrl(array $params = []): string
    {
        return Glide::getMediaUrl($this, $params);
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
            get: fn (): string => $this->resolveSizeUrl(fn (): string => Curator::getUrlProvider()::getThumbnailUrl($this->path)),
        );
    }

    public function mediumUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->resolveSizeUrl(fn (): string => Curator::getUrlProvider()::getMediumUrl($this->path)),
        );
    }

    public function largeUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->resolveSizeUrl(fn (): string => Curator::getUrlProvider()::getLargeUrl($this->path)),
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

    /**
     * Curation entries round-trip through form state, and rows written before entries were checked on save may still
     * point at an older curation folder, such as one orphaned by a rename, so URLs are built for an image one folder
     * below this item's directory on its disk, as long as it isn't another media item's file.
     */
    public function canResolveCurationFile(mixed $disk, mixed $path): bool
    {
        $disk ??= $this->disk;

        if (! is_string($disk) || ! is_string($path) || $disk !== $this->disk) {
            return false;
        }

        $path = ltrim($path, '/');

        if (! static::isPlainRelativePath($path)) {
            return false;
        }

        $directory = static::curationFolder(null, (string) $this->directory);

        if ($directory !== '') {
            if (! str_starts_with($path, $directory . '/')) {
                return false;
            }

            $path = substr($path, strlen($directory) + 1);
        }

        if (substr_count($path, '/') !== 1 || ! in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), static::CURATION_EXTENSIONS, true)) {
            return false;
        }

        return ! $this->isAnotherMediaFile(ltrim($directory . '/' . $path, '/'), ignoringCase: false);
    }

    /**
     * Unscoped, so a tenant or soft-delete scope never hides another item's file. Steps that write also match
     * regardless of case, for disks that ignore it; building a URL matches the exact path, which uses the index.
     */
    public function isAnotherMediaFile(mixed $path, bool $ignoringCase = true): bool
    {
        if (! is_string($path) || blank($path)) {
            return false;
        }

        $path = ltrim($path, '/');

        return $this->newQueryWithoutScopes()
            ->where('disk', $this->disk)
            ->where(function (Builder $query) use ($path, $ignoringCase): void {
                $query->whereIn('path', [$path, '/' . $path]);

                if ($ignoringCase) {
                    $query->orWhereIn(DB::raw('LOWER(path)'), [mb_strtolower($path), '/' . mb_strtolower($path)]);
                }
            })
            ->when($this->exists, fn (Builder $query): Builder => $query->whereKeyNot($this->getKey()))
            ->exists();
    }

    /**
     * Unscoped and regardless of case, like isAnotherMediaFile(), so a folder holding another item's file is never
     * removed with this item.
     */
    public function hasAnotherMediaFileIn(string $folder): bool
    {
        $prefix = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], mb_strtolower(trim($folder, '/'))) . '/%';

        return $this->newQueryWithoutScopes()
            ->where('disk', $this->disk)
            ->where(fn (Builder $query): Builder => $query
                ->whereRaw("LOWER(path) LIKE ? ESCAPE '!'", [$prefix])
                ->orWhereRaw("LOWER(path) LIKE ? ESCAPE '!'", ['/' . $prefix]))
            ->when($this->getKey() !== null, fn (Builder $query): Builder => $query->whereKeyNot($this->getKey()))
            ->exists();
    }

    protected static function isPlainRelativePath(string $path): bool
    {
        if ($path === '' || str_contains($path, '\\') || str_contains($path, "\0")) {
            return false;
        }

        foreach (explode('/', $path) as $segment) {
            if (in_array($segment, ['', '.', '..'], true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Size URLs reach JSON, Livewire state and rendered tables, so for media
     * that isn't public they expire, and the media route only serves that
     * media through an unexpired URL.
     *
     * @param  Closure(): string  $resolve
     */
    protected function resolveSizeUrl(Closure $resolve): string
    {
        if ($this->isPublic()) {
            return $resolve();
        }

        return Glide::withTemporaryUrls($this->disk, $resolve);
    }
}
