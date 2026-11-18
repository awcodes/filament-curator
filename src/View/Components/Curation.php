<?php

namespace Awcodes\Curator\View\Components;

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Support\Helpers;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Curation extends Component
{
    public ?array $curatedMedia = null;

    public function __construct(
        public int | Media | null $media,
        public ?string $curation = null,
    ) {
        if (! $media instanceof Media) {
            $this->media = app(Media::class)::where('id', $media)->first();
        }

        if ($this->media) {
            $curatedMedia = $this->media->getCuration($curation);

            $this->curatedMedia = $this->media->canResolveCurationFile($curatedMedia['disk'] ?? null, $curatedMedia['path'] ?? null)
                ? $curatedMedia
                : null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | string | Closure
    {
        if ($this->curatedMedia) {
            $this->curatedMedia['url'] = Helpers::getUrl(disk: $this->curatedMedia['disk'] ?? $this->media->disk, path: $this->curatedMedia['path']);
        }

        return function (array $data) {
            return 'curator::components.curation';
        };
    }
}
