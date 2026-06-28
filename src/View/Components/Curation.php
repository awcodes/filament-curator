<?php

declare(strict_types=1);

namespace Awcodes\Curator\View\Components;

use Awcodes\Curator\Models\Media;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Curation extends Component
{
    public ?array $curatedMedia = null;

    public function __construct(
        public int|Media|null $media,
        public ?string $curation = null,
    ) {
        if (! $media instanceof Media) {
            $this->media = app(Media::class)::where('id', $media)->first();
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
        if ($this->curatedMedia !== null && $this->curatedMedia !== []) {
            $this->curatedMedia['url'] = Media::resolveUrl($this->curatedMedia['disk'], $this->curatedMedia['path'], $this->curatedMedia['visibility'] ?? null);
        }

        return fn (array $data): string => 'curator::components.curation';
    }
}
