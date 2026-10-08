<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Support\MediaScope;
use enshrined\svgSanitize\Sanitizer;
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

if (! function_exists('is_media_svg')) {
    function is_media_svg(?string $type): bool
    {
        return $type === 'image/svg+xml';
    }
}

if (! function_exists('is_unsafe_inline_media')) {
    /**
     * Whether serving a type inline would let it execute script in the
     * application's origin. Used to stop a content type sniffed from stored
     * bytes deciding how the browser renders media the extension never
     * declared as a document.
     */
    function is_unsafe_inline_media(?string $type): bool
    {
        return in_array(strtolower((string) $type), [
            'image/svg+xml',
            'text/html',
            'application/xhtml+xml',
            'application/xml',
            'text/xml',
            'text/javascript',
            'application/javascript',
            'application/vnd.mozilla.xul+xml',
        ], true);
    }
}

if (! function_exists('sanitize_svg')) {
    /**
     * Strip scripts, event handlers and remote references from SVG markup so it
     * cannot execute JavaScript when served inline as a top-level document.
     */
    function sanitize_svg(string $svg): string
    {
        $sanitizer = new Sanitizer;
        $sanitizer->removeRemoteReferences(true);

        // The sanitizer throws, rather than returning false, for some markup it
        // rejects, such as an XHTML document wrapping an <svg> element.
        try {
            $clean = $sanitizer->sanitize($svg);
        } catch (\Throwable) {
            return '';
        }

        // The sanitizer returns false when the markup cannot be parsed. Fail
        // closed by returning an empty string rather than the untrusted original.
        return $clean === false ? '' : $clean;
    }
}

if (! function_exists('get_media_items')) {
    /**
     * Load media for some ids, in their order. With a scope, the ids are always
     * looked up again within it, even when full records or their arrays are
     * passed, so an id the scope doesn't allow never loads.
     */
    function get_media_items(array | Media | int | string $ids, ?MediaScope $scope = null): Collection | array
    {
        if ($scope instanceof MediaScope) {
            return $scope->resolve($ids);
        }

        if (! is_array($ids) && ! $ids instanceof Media) {
            $ids = [$ids];
        }

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
