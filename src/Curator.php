<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Facades\CuratorConfig;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Collection;

class Curator
{
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
            return CuratorConfig::getMediaModel()::whereIn('id', $ids)
                ->get()
                ->sortBy(function ($model) use ($ids) {
                    return array_search($model->id, $ids);
                });
        }

        return [];
    }
}
