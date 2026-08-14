<?php

declare(strict_types=1);

namespace Awcodes\Curator\Http\Controllers;

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Filesystem\FilesystemException;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;

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

        $media = App::make(Media::class)::query()
            ->where('path', $path)
            ->first();

        abort_unless(filled($media), 404);

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

            return $disk->response(
                path: $media->path,
                name: null,
                headers: [
                    'Content-Type' => $type,
                    'X-Content-Type-Options' => 'nosniff',
                ],
                disposition: $disposition,
            );
        }

        return $glide->getServer()->getImageResponse($path, request()->all());
    }
}
