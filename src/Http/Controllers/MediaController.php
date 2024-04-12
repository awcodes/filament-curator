<?php

namespace Awcodes\Curator\Http\Controllers;

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;

class MediaController extends Controller
{
    public function show(Request $request, $path, GlideManager $glide)
    {
        try {
            SignatureFactory::create($glide->getToken())
                ->validateRequest(
                    path: $glide->getBasePath() . '/' . $path,
                    params: $request->all()
                );
        } catch (SignatureException $e) {
            abort(403);
        }

        $media = Media::query()
            ->where('path', $path)
            ->first();

        abort_unless(filled($media), 404);

        if (! Curator::isResizable($media->ext)) {
            return Storage::disk($media->disk)->response($media->path);
        }

        return $glide->getServer()->getImageResponse($path, request()->all());
    }
}
