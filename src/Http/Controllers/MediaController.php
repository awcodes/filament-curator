<?php

namespace FilamentCurator\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $files = resolve(config('filament-curator.model'))->where('id', '<>',  $request->media_id)->latest()->paginate(25);

        if ($request->has('media_id') && !$request->has('page')) {
            $selected = resolve(config('filament-curator.model'))->where('id', $request->media_id)->first();
            $files->prepend($selected);
        }

        return response()->json($files, 200);
    }

    public function search(Request $request)
    {
        $files = resolve(config('filament-curator.model'))->where('filename', 'like', '%' . $request->q . '%')
            ->orWhere('alt', 'like', '%' . $request->q . '%')
            ->orWhere('caption', 'like', '%' . $request->q . '%')
            ->orWhere('description', 'like', '%' . $request->q . '%')
            ->paginate(50);

        return response()->json($files, 200);
    }
}
