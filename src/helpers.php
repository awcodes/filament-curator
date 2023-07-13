<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Collection;

if (! function_exists('Curator\is_media_resizable')) {
    function is_media_resizable(string $ext): bool
    {
        return in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }
}

if (! function_exists('Curator\get_media_items')) {
    function get_media_items(array | Media | int $ids): Collection | array
    {
        if ($ids instanceof Media) {
            return [$ids];
        }

        $ids = array_values($ids);

        if (isset($ids[0]['id'])) {
            return $ids;
        }

        if (filled($ids)) {
            return app(Media::class)::whereIn('id', $ids)
                ->get()
                ->sortBy(function ($model) use ($ids) {
                    return array_search($model->id, $ids);
                });
        }

        return [];
    }
}
