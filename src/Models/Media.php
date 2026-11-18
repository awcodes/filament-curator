<?php

namespace Awcodes\Curator\Models;

use Awcodes\Curator\Concerns\HasPackageFactory;
use Awcodes\Curator\Support\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Glide\Urls\UrlBuilderFactory;

use function Awcodes\Curator\is_media_resizable;

/**
 * @property int $id
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
 * @property string $alt
 * @property string $title
 * @property string $description
 * @property string $caption
 * @property array $exif
 * @property array $curations
 * @property string $url
 * @property string $thumbnail_url
 * @property string $medium_url
 * @property string $large_url
 * @property bool $resizable
 * @property string $size_for_humans
 * @property string $pretty_name
 */
class Media extends Model
{
    use HasPackageFactory;

    protected $guarded = [];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'size' => 'integer',
        'curations' => 'array',
    ];

    protected $appends = [
        'url',
        'thumbnail_url',
        'medium_url',
        'large_url',
        'resizable',
        'size_for_humans',
        'pretty_name',
    ];

    /**
     * A prefix to identify base64 encoded strings in exif data
     */
    protected const BASE64_PREFIX = 'encoded@base64:';

    /**
     * Extensions a curation can be saved as, current and past, which bounds what a stored curation can point at.
     */
    protected const CURATION_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif', 'bmp'];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                return Helpers::getUrl(disk: $this->disk, path: $this->path);
            },
        );
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getSignedUrl(['w' => 200, 'h' => 200, 'fit' => 'crop', 'fm' => 'webp']),
        );
    }

    protected function mediumUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getSignedUrl(['w' => 640, 'h' => 640, 'fit' => 'crop', 'fm' => 'webp']),
        );
    }

    protected function largeUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getSignedUrl(['w' => 1024, 'h' => 1024, 'fit' => 'contain', 'fm' => 'webp']),
        );
    }

    protected function fullPath(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk($this->disk)->path($this->path),
        );
    }

    protected function resizable(): Attribute
    {
        return Attribute::make(
            get: fn () => is_media_resizable($this->type),
        );
    }

    protected function sizeForHumans(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getSizeForHumans()
        );
    }

    protected function prettyName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getPrettyName()
        );
    }

    /**
     * Cast exif data safely, dealing with any non-utf8 characters
     */
    protected function exif(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($this->decodeExif($value), true),
            set: fn ($value) => json_encode($this->encodeExif($value)),
        );
    }

    public function getPrettyName(): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        return $this->name . '.' . $this->ext;
    }

    public function getSizeForHumans(int $precision = 1): string
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
        $size = $this->size;
        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    public function getSignedUrl(array $params = [], bool $force = false): string
    {
        if (! $force) {
            if (
                ! $this->resizable ||
                in_array($this->disk, config('curator.cloud_disks')) ||
                ! Storage::disk($this->disk)->exists($this->path)
            ) {
                return $this->url;
            }
        }

        $routeBasePath = Str::of(config('curator.glide.route_path', 'curator'))
            ->trim('/')
            ->prepend('/')
            ->append('/')
            ->toString();

        $urlBuilder = UrlBuilderFactory::create($routeBasePath, config('app.key'));

        return $urlBuilder->getUrl($this->path, $params);
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

    /**
     * Rows written before entries were checked on save may still point at an older curation folder, such as one
     * left behind by a rename, so URLs are built for an image one folder below this item's directory on its disk, as
     * long as it isn't another media item's file.
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
     * Unscoped and regardless of case, like isAnotherMediaFile(), so a folder holding any other item's file is found.
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
     * Recursively encode exif data safely, dealing with any non-utf8 characters
     */
    protected function encodeExif(mixed $value): mixed
    {
        if (! $value) {
            return null;
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->encodeExif($item), $value);
        }

        return $this->needsBase64($value) ? self::BASE64_PREFIX . base64_encode($value) : $value;
    }

    /**
     * Decode exif data safely, dealing with any base64 encoded strings
     */
    protected function decodeExif(mixed $value): mixed
    {
        if (! $value) {
            return null;
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->decodeExif($item), $value);
        }

        if (str_starts_with($value, self::BASE64_PREFIX)) {
            return base64_decode(substr($value, strlen(self::BASE64_PREFIX)));
        }

        return $value;
    }

    /**
     * Detect strings that should be encoded as base64
     */
    protected function needsBase64(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        // check for invalid encoding (likely binary)
        if (! mb_detect_encoding($value, 'UTF-8', true)) {
            return true;
        }

        // encode as base64 if there are any non-printable characters
        $nonPrintable = preg_replace('/[[:print:]\s]/u', '', $value);

        return strlen($nonPrintable) > strlen($value);
    }
}
