<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

if (! function_exists('is_media_resizable')) {
    function is_media_resizable(string $type): bool
    {
        return in_array($type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp']);
    }
}

if (! function_exists('get_media_items')) {
    function get_media_items(array | Media | int $ids): Collection | array
    {
        $mediaModel = config('curator.model');

        if ($ids instanceof $mediaModel) {
            return [$ids];
        }

        if (is_array($ids) && is_related_to_media_through_pivot(current($ids))) {
            $mediaIds = collect($ids)->map(fn ($model) => $model?->media_id)->toArray();

            return config('curator.model')::whereIn('id', $mediaIds)->get();
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

if (! function_exists('is_related_to_media_through_pivot')) {
    function is_related_to_media_through_pivot(mixed $type): bool
    {
        $intermediateModelType = config('curator.intermediate_model');

        return (! is_null(config('curator.intermediate_model'))) && $type instanceof $intermediateModelType;
    }
}

if (! function_exists('is_panel_auth_route')) {
    function is_panel_auth_route(): bool
    {
        $authRoutes = [
            '/login',
            '/password-reset',
            '/register',
            '/email-verification',
        ];

        return Str::of(Request::path())->contains($authRoutes);
    }
}
