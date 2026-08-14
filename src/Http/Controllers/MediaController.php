<?php

namespace Awcodes\Curator\Http\Controllers;

use Awcodes\Curator\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\Mime\MimeTypes;

use function Awcodes\Curator\is_unsafe_inline_media;

class MediaController extends Controller
{
    public function show(Request $request, $path)
    {
        $routeBasePath = Str::of(config('curator.glide.route_path', 'curator'))
            ->trim('/')
            ->prepend('/')
            ->append('/')
            ->toString();

        try {
            SignatureFactory::create(config('app.key'))->validateRequest($routeBasePath . $path, $request->all());
        } catch (SignatureException $e) {
            abort(403);
        } catch (FileNotFoundException $e) {
            abort(404);
        }

        $media = app(Media::class)::query()->where('path', $path)->first();

        if ($media && ! $media->resizable) {
            // Media that bypasses Glide is streamed straight from disk, and
            // Flysystem types it by sniffing the bytes. SVG markup stored under
            // another extension would therefore come back as image/svg+xml and
            // render as a document in this origin, so pin the type the stored
            // extension declares instead.
            $disk = Storage::disk($media->disk);
            $disposition = 'inline';
            $type = MimeTypes::getDefault()->getMimeTypes(strtolower((string) $media->ext))[0] ?? null;

            if ($type === null) {
                $sniffed = $disk->mimeType($media->path) ?: null;

                if ($sniffed === null || is_unsafe_inline_media($sniffed)) {
                    $type = 'application/octet-stream';
                    $disposition = 'attachment';
                } else {
                    $type = $sniffed;
                }
            }

            return $disk->response(
                $media->path,
                null,
                [
                    'Content-Type' => $type,
                    'X-Content-Type-Options' => 'nosniff',
                ],
                $disposition,
            );
        }

        $server = app(config('curator.glide.server'))->getFactory();
        $server->setBaseUrl($routeBasePath);

        return $server->getImageResponse($path, request()->all());
    }
}
