<?php

namespace Awcodes\Curator\Http\Controllers;

use Awcodes\Curator\Facades\Curator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $mediaModel = Curator::getMediaModel();
        $selected = $request->has('media') ? explode(',', $request->media) : [];

        $files = $mediaModel::when($selected, function($query, $selected) {
                return $query->whereNotIn('id', $selected);
            })
            ->latest()
            ->paginate(25);

        if ($selected && ! $request->has('page')) {
            $mediaModel::whereIn('id', $selected)
                ->orderByRaw('FIELD(id, '.implode(', ', $selected).')')
                ->get()
                ->reverse()
                ->map(function($item) use ($files) {
                    $files->prepend($item);
                });
        }

        $files->each(function($item) {
            $urlBuilder = UrlBuilderFactory::create('/curator/', config('app.key'));
            $item->signedUrl = $urlBuilder->getUrl($item->path, ['w' => 300, 'h' => 300, 'fit' => 'crop', 'fm' => 'webp']);
        });

        return response()->json($files);
    }

    public function search(Request $request)
    {
        $files = Curator::getMediaModel()::where('name', 'like', '%'.$request->query('q').'%')
            ->orWhere('alt', 'like', '%'.$request->query('q').'%')
            ->orWhere('caption', 'like', '%'.$request->query('q').'%')
            ->orWhere('description', 'like', '%'.$request->query('q').'%')
            ->paginate(50);

        $files->each(function($item) {
            $urlBuilder = UrlBuilderFactory::create('/curator/', config('app.key'));
            $item->signedUrl = $urlBuilder->getUrl($item->path, ['w' => 300, 'h' => 300, 'fit' => 'crop', 'fm' => 'webp']);
        });

        return response()->json($files);
    }

    public function show(Request $request, $path)
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
