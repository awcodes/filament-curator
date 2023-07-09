<?php

namespace Awcodes\Curator\Components\Tables;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Closure;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class CuratorColumn extends ImageColumn
{
    protected int|Closure|null $limit = null;

    protected int|Closure|null $overlap = null;

    protected int|Closure|null $resolution = null;

    protected int|Closure|null $ring = null;

    protected string $view = 'curator::components.tables.curator-column';

    public function getLimit(): ?int
    {
        return $this->evaluate($this->limit);
    }

    public function getMedia(): Media|Collection|array|null
    {
        $record = $this->getRecord();

        if (!is_a($record, Media::class)) {
            $state = $this->getState();

            if (is_a($state, Collection::class)) {
                return $state->take($this->limit);
            }

            if (is_a($state, Media::class)) {
                return Arr::wrap($state);
            }

            $state = Arr::wrap($state);
            return Curator::getMedia(array_slice($state, 0, $this->limit));
        }

        return Arr::wrap($record);
    }

    public function getOverlap(): ?int
    {
        return $this->evaluate($this->overlap);
    }

    public function getResolution(): ?int
    {
        return $this->evaluate($this->resolution);
    }

    public function getRing(): ?int
    {
        return $this->evaluate($this->ring);
    }

    public function limit(int|Closure|null $limit = 3): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function overlap(int|Closure|null $overlap): static
    {
        $this->overlap = $overlap;

        return $this;
    }

    public function resolution(int|Closure|null $resolution): static
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function ring(string|Closure|null $ring): static
    {
        $this->ring = $ring;

        return $this;
    }
}