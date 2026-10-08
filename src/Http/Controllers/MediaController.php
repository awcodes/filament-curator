<?php

declare(strict_types=1);

namespace Awcodes\Curator\Http\Controllers;

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    /**
     * @throws FilesystemException
     * @throws FileNotFoundException
     * @throws BindingResolutionException
     */
    public function show(Request $request, string $path, GlideManager $glide)
    {
        try {
            SignatureFactory::create($glide->getToken())
                ->validateRequest(
                    path: $glide->getBasePath() . '/' . $path,
                    params: $request->all()
                );
        } catch (SignatureException) {
            abort(403);
        }

        $expires = $this->getExpiration($request);

        abort_if($expires === false, 403);

        $media = $this->resolveMedia($path, $request->query(GlideManager::DISK_PARAMETER), $expires !== null);

        abort_unless($media instanceof Media, 404);

        if (! Curator::isResizable($media->ext)) {
            // Media that bypasses Glide is streamed straight from disk, so pin the
            // declared content type and force restricted types to download rather
            // than render as a document in the application's origin.
            $disk = Storage::disk($media->disk);
            $disposition = Curator::isRestricted($media->ext) ? 'attachment' : 'inline';
            $type = MimeType::tryFromExtension($media->ext)?->value;

            if ($type === null) {
                // Nothing declares what this is, so fall back to sniffing — but
                // never let sniffed bytes talk the browser into rendering a
                // document. Storing SVG markup under an extension the enum does
                // not know would otherwise be served as image/svg+xml inline.
                $sniffed = $disk->mimeType($media->path) ?: null;

                if ($sniffed === null || Curator::isUnsafeInlineMimeType($sniffed)) {
                    $type = MimeType::ApplicationOctetStream->value;
                    $disposition = 'attachment';
                } else {
                    $type = $sniffed;
                }
            }

            $response = $disk->response(
                path: $media->path,
                name: null,
                headers: [
                    'Content-Type' => $type,
                    'X-Content-Type-Options' => 'nosniff',
                ],
                disposition: $disposition,
            );

            return $expires === null ? $response : $this->keepPrivate($response, $expires);
        }

        $response = $glide->getServer($media->disk)->getImageResponse(
            $path,
            Arr::except($request->query(), [GlideManager::EXPIRES_PARAMETER, GlideManager::DISK_PARAMETER]),
        );

        return $expires === null ? $response : $this->keepPrivate($response, $expires);
    }

    /**
     * Null for a permanent URL, false for one that is malformed or has
     * expired, otherwise the timestamp it expires at. The signature has
     * already been checked, so the timestamp is the one the URL was issued with.
     */
    protected function getExpiration(Request $request): int | false | null
    {
        $expires = $request->query(GlideManager::EXPIRES_PARAMETER);

        if ($expires === null) {
            return null;
        }

        if (! is_string($expires) || ! ctype_digit($expires)) {
            return false;
        }

        $expires = (int) $expires;

        return $expires > now()->getTimestamp() ? $expires : false;
    }

    /**
     * A permanent URL only ever serves public media. A temporary URL names the
     * disk it was issued for, so a path stored on more than one disk resolves
     * to the record the URL was made for.
     */
    protected function resolveMedia(string $path, mixed $disk, bool $isTemporary): ?Media
    {
        $query = App::make(Media::class)::query()->where('path', $path);

        if (is_string($disk) && filled($disk)) {
            $query->where('disk', $disk);
        }

        if (! $isTemporary) {
            $query->where(function (Builder $query): void {
                $query->where('visibility', 'public');

                if (Media::isPublicVisibility(null)) {
                    $query->orWhereNull('visibility')->orWhere('visibility', '');
                }
            });
        }

        $defaultDisk = (string) config('curator.default_disk');

        return $query
            ->orderByRaw('case when disk = ? then 0 else 1 end', [$defaultDisk])
            ->orderBy($query->getModel()->getQualifiedKeyName())
            ->first();
    }

    /**
     * Responses to a temporary URL can be kept by the browser until the URL
     * expires, but never by a shared cache.
     */
    protected function keepPrivate(mixed $response, int $expires): mixed
    {
        if (! $response instanceof Response) {
            return $response;
        }

        $response->headers->remove('Expires');
        $response->headers->set('Cache-Control', 'private, max-age=' . max(0, $expires - now()->getTimestamp()));

        return $response;
    }
}
