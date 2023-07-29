<?php

namespace Awcodes\Curator\Components\Tables;

use Awcodes\Curator\Models\Media;
use Closure;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use function Awcodes\Curator\get_media_items;

class CuratorColumn extends ImageColumn
{
    protected int|Closure|null $resolution = null;

    protected string $view = 'curator::components.tables.curator-column';

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

            return get_media_items(array_slice($state, 0, $this->limit));
        }

        return Arr::wrap($record);
    }

    public function getResolution(): ?int
    {
        return $this->evaluate($this->resolution);
    }

    public function resolution(int|Closure|null $resolution): static
    {
        $this->resolution = $resolution;

        return $this;
    }
}
