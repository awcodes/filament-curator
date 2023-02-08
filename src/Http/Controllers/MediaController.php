<?php

namespace Awcodes\Curator\Http\Controllers;

use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $files = Media::where('id', '<>', $request->media_id)->latest()->paginate(25);

        if ($request->has('media_id') && ! $request->has('page')) {
            $selected = Media::where('id', $request->media_id)->first();
            $files->prepend($selected);
        }

        return response()->json($files, 200);
    }

    public function search(Request $request)
    {
        $files = Media::where('name', 'like', '%'.$request->q.'%')
            ->orWhere('alt', 'like', '%'.$request->q.'%')
            ->orWhere('caption', 'like', '%'.$request->q.'%')
            ->orWhere('description', 'like', '%'.$request->q.'%')
            ->paginate(50);

        return response()->json($files, 200);
    }

    public function show(Request $request, Filesystem $filesystem, $path)
    {
        try {
            SignatureFactory::create(config('app.key'))->validateRequest('/curator/' . $path, $request->all());
        } catch (SignatureException $e) {
            abort(403);
        } catch (FileNotFoundException $e) {
            abort(404);
        }

        $server = app('curator')->getGlideServer();
        $server->setBaseUrl('/curator/');
        return $server->getImageResponse($path, request()->all());
    }
}
