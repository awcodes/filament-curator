<?php

namespace Awcodes\Curator\Http\Controllers;

use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

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

    public function show(Filesystem $filesystem, $path)
    {
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory(app('request')),
            'source' => $filesystem->getDriver(),
            'source_path_prefix' => 'public',
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => '.cache',
            'base_url' => 'curator',
        ]);

        return $server->getImageResponse($path, request()->all());
    }
}
