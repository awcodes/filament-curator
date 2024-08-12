<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

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

        if (is_array($ids) && is_related_to_media_through_pivot(get_class(current($ids)), Media::class)) {
            $mediaIds = collect($ids)->map(fn ($model) => $model?->media_id)->toArray();

            return Media::whereIn('id', $mediaIds)->get();
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
    function is_related_to_media_through_pivot(string $modelClass, string $relatedClass): bool
    {
        $model = new $modelClass;
        $reflector = new \ReflectionClass($model);
        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods ?? [] as $method) {
            if ($method?->class === $modelClass) {
                $returnType = $method?->getReturnType();
                if ($returnType) {
                    $relationInstance = $model->{$method->getName()}();
                    if ($relationInstance instanceof BelongsTo &&
                        get_class($relationInstance->getRelated()) === $relatedClass) {
                        return true;
                    }
                }
            }
        }

        return false;
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
