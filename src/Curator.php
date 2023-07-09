<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Models\Media;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Collection;

class Curator
{
    public function isResizable(string $ext): bool
    {
        return in_array($ext, ['jpeg', 'jpg', 'png', 'webp', 'bmp']);
    }

    public function getMedia(array|Media|int $ids): Collection|array
    {
        if ($ids instanceof Media) {
            return [$ids];
        }

        $ids = array_values($ids);

        if (isset($ids[0]['id'])) {
            return $ids;
        }

        if (filled($ids)) {
            return app(config('curator.media_model'))::whereIn('id', $ids)
                ->get()
                ->sortBy(function ($model) use ($ids) {
                    return array_search($model->id, $ids);
                });
        }

        return [];
    }

    public function getModel(): Media
    {
        return app(config('curator.media_model'));
    }

    public function getModelResource(): Resource
    {
        return app(config('curator.media_resource'));
    }
}
