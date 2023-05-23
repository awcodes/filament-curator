<?php

namespace Awcodes\Curator\Http\Controllers;

use Awcodes\Curator\Facades\Curator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $mediaModel = Curator::getMediaModel();

        $files = $mediaModel::where('id', '<>', $request->media_id)->latest()->paginate(25);

        if ($request->has('media_id') && ! $request->has('page')) {
            $selected = $mediaModel::where('id', $request->media_id)->first();
            $files->prepend($selected);
        }

        return response()->json($files);
    }

    public function search(Request $request)
    {
        $files = Curator::getMediaModel()::where('name', 'like', '%'.$request->query('q').'%')
            ->orWhere('alt', 'like', '%'.$request->query('q').'%')
            ->orWhere('caption', 'like', '%'.$request->query('q').'%')
            ->orWhere('description', 'like', '%'.$request->query('q').'%')
            ->paginate(50);

        return response()->json($files);
    }

    public function show(Request $request, $path)
    {
        try {
            SignatureFactory::create(config('app.key'))->validateRequest('/curator/'.$path, $request->all());
        } catch (SignatureException $e) {
            abort(403);
        } catch (FileNotFoundException $e) {
            abort(404);
        }

        $media = Curator::getMediaModel()::where('path', $path)->first();

        if ($media && ! Curator::isResizable($media->ext)) {
            return Storage::disk($media->disk)->response($media->path);
        }

        $server = app('curator')->getGlideServer();
        $server->setBaseUrl('/curator/');

        return $server->getImageResponse($path, request()->all());
    }
}
