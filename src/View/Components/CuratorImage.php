<?php

namespace FilamentCurator\View\Components;

use Closure;
use FilamentCurator\Models\Media;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CuratorImage extends Component
{
    public string | Media | null $media;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(int $mediaId)
    {
        $this->media = config('filament-curator.model')::where('id', $mediaId)->first();

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render(): View|string|Closure
    {
        return view('filament-curator::components.curator-image', [
            'media' => $this->media
        ]);
    }
}
