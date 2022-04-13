<?php

namespace FilamentCurator\Models;

use Cloudinary\Cloudinary;
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
    ];

    public function getUrlAttribute()
    {
        if ($this->disk == 'cloudinary') {
            return (string) (new Cloudinary())->image($this->public_id)
                ->delivery(Delivery::format(Format::auto()))
                ->delivery(Delivery::quality(Quality::auto()));
        } else {
            return Storage::disk($this->disk)->url($this->filename);
        }
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getUrlForSize('thumbnail');
    }

    public function getMediumUrlAttribute()
    {
        return $this->getUrlForSize('medium');
    }

    public function getlargeUrlAttribute()
    {
        return $this->getUrlForSize('large');
    }

    public function getUrlForSize(string $size = 'large')
    {
        if ($this->disk == 'cloudinary') {
            $sizes = config('filament-curator.sizes');
            return (string) (new Cloudinary())->image($this->public_id)
                ->resize(
                    Resize::fill()
                        ->width($sizes[$size]['width'])
                        ->height($sizes[$size]['height'])
                        ->gravity(Gravity::focusOn(FocusOn::faces()))
                )
                ->delivery(Delivery::format(Format::auto()))
                ->delivery(Delivery::quality(Quality::auto()));
        } else {
            $pathinfo = pathinfo($this->filename);
            return Storage::disk($this->disk)->url($this->public_id . '-' . $size . '.' . $this->ext);
        }
    }

    public function sizeForHumans(int $precision = 1): string
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $this->size > 1024; $i++) {
            $this->size /= 1024;
        }

        return round($this->size, $precision) . ' ' . $units[$i];
    }
}
