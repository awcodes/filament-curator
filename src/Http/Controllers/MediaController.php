<?php

namespace FilamentCurator\Http\Controllers;

use FilamentCurator\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MediaController extends Controller
{
    public function index()
    {
        $files = Media::latest()->paginate(25);

        return response()->json($files, 200);
    }

    public function search(Request $request)
    {
        $files = Media::where('filename', 'like', '%' . $request->q . '%')
            ->orWhere('alt', 'like', '%' . $request->q . '%')
            ->orWhere('caption', 'like', '%' . $request->q . '%')
            ->orWhere('description', 'like', '%' . $request->q . '%')
            ->paginate(25);

        return response()->json($files, 200);
    }
}
