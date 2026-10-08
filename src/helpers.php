<?php

declare(strict_types=1);

use Awcodes\Curator\Config\CurationManager;
use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Glide\GlideBuilder;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Support\MediaScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

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
        return in_array(mb_strtolower($ext), ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }
}

if (! function_exists('get_media_items')) {
    /**
     * Load media for some ids, in their order. With a scope, the ids are always looked up again within it, even when
     * full records or their arrays are passed, so an id the scope doesn't allow never loads.
     */
    function get_media_items(array | Media | int | string $ids, ?MediaScope $scope = null): Collection | array
    {
        if ($scope instanceof MediaScope) {
            return $scope->resolve($ids);
        }

        if ($ids instanceof Media) {
            return [$ids];
        }

        $ids = array_values(Arr::wrap($ids));

        if (isset($ids[0]['id'])) {
            return $ids;
        }

        if (filled($ids)) {
            return app(Media::class)::whereIn('id', $ids)
                ->get()
                ->sortBy(fn ($model): int | false => array_search($model->id, $ids));
        }

        return [];
    }
}
