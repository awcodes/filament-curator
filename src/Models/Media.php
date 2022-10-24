<?php

namespace FilamentCurator\Models;

use FilamentCurator\Facades\CuratorThumbnails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use stdClass;

class Media extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function (Media $media) {
            if (is_array($media->filename) || $media->filename instanceof stdClass) {
                foreach ($media->filename as $k => $v) {
                    $media->{$k} = $v;
                }
            }
        });

        static::created(function (Media $media) {
            $media->refresh();
            CuratorThumbnails::generate($media);
        });

        static::deleted(function (Media $media) {
            CuratorThumbnails::destroy($media);
            Storage::disk($media->disk)->delete($media->filename);

            if (count(Storage::disk($media->disk)->allFiles($media->directory)) == 0) {
                Storage::disk($media->disk)->deleteDirectory($media->directory);
            }
        });
    }

    protected $fillable = [
        'public_id',
        'filename',
        'ext',
        'type',
        'alt',
        'title',
        'description',
        'caption',
        'width',
        'height',
        'disk',
        'directory',
        'size',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
    ];

    protected $appends = [
        'url',
        'thumbnail_url',
        'medium_url',
        'large_url',
        'size_for_humans',
        'hasSizes',
    ];

    public function getHasSizesAttribute(): bool
    {
        return CuratorThumbnails::hasSizes($this->ext);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->filename);
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->getSizeUrl('thumbnail');
    }

    public function getMediumUrlAttribute(): string
    {
        return $this->getSizeUrl('medium');
    }

    public function getlargeUrlAttribute(): string
    {
        return $this->getSizeUrl('large');
    }

    public function getSizeUrl(string $size): string
    {
        $sizes = CuratorThumbnails::getSizes();
        if (Arr::exists($sizes, $size) && $this->has_sizes) {
            return $this->getUrlForSize($size);
        }

        return Storage::disk($this->disk)->url($this->filename);
    }

    public function getUrlForSize(string $size = 'large'): string
    {
        return Storage::disk($this->disk)->url($this->public_id . '-' . $size . '.' . $this->ext);
    }

    public function getSizeForHumansAttribute(): string
    {
        return $this->sizeForHumans();
    }

    public function sizeForHumans(int $precision = 1): string
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
        $size = $this->size;
        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }
}
