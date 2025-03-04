<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

if (! function_exists('is_media_resizable')) {
    function is_media_resizable(?string $type): bool
    {
        if (empty($type)) {
            return false;
        }

        return in_array($type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp']);
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
