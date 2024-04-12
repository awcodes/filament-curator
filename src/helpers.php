<?php

use Awcodes\Curator\Config\CurationManager;
use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Glide\GlideBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Collection;

if (! function_exists('curator')) {
    function curator(): CuratorManager
    {
        return app(CuratorManager::class);
    }
}

if (! function_exists('glide')) {
    function glide(): GlideManager
    {
        return app(GlideManager::class);
    }
}

if (! function_exists('curation')) {
    function curation(): CurationManager
    {
        return app(CurationManager::class);
    }
}

if (! function_exists('glide_builder')) {
    function glide_builder(): GlideBuilder
    {
        return app(GlideBuilder::class);
    }
}

if (! function_exists('is_media_resizable')) {
    function is_media_resizable(string $ext): bool
    {
        return in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }
}

if (! function_exists('get_media_items')) {
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
