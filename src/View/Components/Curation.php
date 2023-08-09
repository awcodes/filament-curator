<?php

namespace RocketFirm\Curator\View\Components;

use RocketFirm\Curator\Facades\Curator;
use RocketFirm\Curator\Models\Media;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class Curation extends Component
{
    public array|null $curatedMedia = null;

    public function __construct(
        public int|Media|null $media,
        public string|null $curation = null,
    ) {
        if (! $media instanceof Media) {
            $this->media = Curator::getMediaModel()::where('id', $media)->first();
        }

        if ($this->media) {
            $this->curatedMedia = $this->media->getCuration($curation);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|string|Closure
    {
        if ($this->curatedMedia) {
            $this->curatedMedia['url'] = Storage::disk($this->curatedMedia['disk'])->url($this->curatedMedia['path']);
        }

        return function (array $data) {
            return 'curator::components.curation';
        };
    }
}
