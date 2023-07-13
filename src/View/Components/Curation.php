<?php

namespace Awcodes\Curator\View\Components;

use Awcodes\Curator\Config\CuratorConfig;
use Awcodes\Curator\Models\Media;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class Curation extends Component
{
    public array|null $curatedMedia = null;

    public function __construct(
        public int|Media|null $media,
        public string|null    $curation = null,
    )
    {
        if (!$media instanceof Media) {
            $this->media = app(CuratorConfig::class)->getMediaModel()::where('id', $media)->first();
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
