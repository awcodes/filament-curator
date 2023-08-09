<?php

namespace RocketFirm\Curator\Http\Controllers;

use RocketFirm\Curator\Facades\Curator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
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
            ->when($request->has('directory'), function($query) use ($request) {
                return $query->where('directory', $request->directory);
            })
            ->when($request->has('types'), function($query) use ($request) {
                $types = explode(',', str_replace(' ', '+', $request->types));
                $query = $query->whereIn('type', $types);
                $wildcardTypes = collect($types)->filter(fn ($type) => str_contains($type, '*'));
                $wildcardTypes?->map(fn($type) => $query->orWhere('type', 'LIKE', str_replace('*', '%', $type)));
                return $query;
            })
            ->latest()
            ->paginate(25);

        if ($selected && ! $request->has('page')) {
            $mediaModel::whereIn('id', $selected)
                ->get()
                ->sortBy(function ($model) use ($selected) {
                    return array_search($model->id, $selected);
                })
                ->reverse()
                ->map(function($item) use ($files) {
                    $files->prepend($item);
                });
        }

        return response()->json($files);
    }

    public function search(Request $request)
    {
        $files = Curator::getMediaModel()::query()
            ->when($request->has('directory'), function($query) use ($request) {
                return $query->where('directory', $request->query('directory'));
            })
            ->when($request->query('q'), function ($query) use ($request) {
                return $query->where('name', 'like', '%'.$request->query('q').'%')
                    ->orWhere('alt', 'like', '%'.$request->query('q').'%')
                    ->orWhere('caption', 'like', '%'.$request->query('q').'%')
                    ->orWhere('description', 'like', '%'.$request->query('q').'%');
            })
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
