<?php

namespace FilamentCurator\Models;

use Cloudinary\Cloudinary;
use Illuminate\Support\Arr;
use Cloudinary\Transformation\Format;
use Cloudinary\Transformation\Resize;
use Intervention\Image\Facades\Image;
use Cloudinary\Transformation\FocusOn;
use Cloudinary\Transformation\Gravity;
use Cloudinary\Transformation\Quality;
use Cloudinary\Transformation\Delivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function (Media $media) {
            foreach ($media->filename as $k => $v) {
                $media->{$k} = $v;
            }
        });

        static::created(function (Media $media) {
            $media->refresh();
            self::generateThumbs($media);
        });

        static::deleted(function (Media $media) {
            $pathinfo = pathinfo($media->filename);
            foreach (config('filament-curator.sizes') as $name => $data) {
                Storage::disk($media->disk)->delete($pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $name . '.' . $media->ext);
            }
            Storage::disk($media->disk)->delete($media->filename);
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
    ];

    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->filename);
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getSizeUrl('thumbnail');
    }

    public function getMediumUrlAttribute()
    {
        return $this->getSizeUrl('medium');
    }

    public function getlargeUrlAttribute()
    {
        return $this->getSizeUrl('large');
    }

    public function getSizeUrl(string $size): string
    {
        $sizes = config('filament-curator.sizes');
        if (Arr::exists($sizes, $size)) {
            return $this->getUrlForSize($size);
        }

        return Storage::disk($this->disk)->url($this->filename);
    }

    public function getUrlForSize(string $size = 'large')
    {
        return Storage::disk($this->disk)->url($this->public_id . '-' . $size . '.' . $this->ext);
    }

    public function getSizeForHumansAttribute()
    {
        return $this->sizeForHumans();
    }

    public function sizeForHumans(int $precision = 1): string
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $this->size > 1024; $i++) {
            $this->size /= 1024;
        }

        return round($this->size, $precision) . ' ' . $units[$i];
    }

    private static function generateThumbs(Media $media): void
    {
        $pathinfo = pathinfo($media->filename);
        foreach (config('filament-curator.sizes') as $name => $mediaSize) {
            $image = Image::make(Storage::disk($media->disk)->path($media->filename));

            if ($mediaSize['width'] == $mediaSize['height']) {
                $image->fit($mediaSize['width']);
            } else {
                $image->resize($mediaSize['width'], $mediaSize['height'], function ($constraint) use ($mediaSize) {
                    if (!$mediaSize['height']) {
                        $constraint->aspectRatio();
                    }
                });
            }

            $image->encode(null, $mediaSize['quality']);
            Storage::disk($media->disk)->put($pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $name . '.' . $media->ext, $image);
        }
    }
}
