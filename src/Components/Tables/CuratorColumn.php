<?php

namespace Awcodes\Curator\Components\Tables;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;

class CuratorColumn extends ImageColumn
{
    protected string $view = 'curator::components.tables.curator-column';

    public function isImage(): bool
    {
        $state = $this->getState();

        if (filled($state)) {
            if (is_a($state, Media::class)) {
                $url = $state->path;
            } else {
                $url = $state;
            }

            $ext = Str::of($url)->afterLast('.');

            return Curator::isResizable($ext);
        }

        return false;
    }

    public function getMedia(): ?Media
    {
        $record = $this->getRecord();

        if (! is_a($record, Media::class)) {
            return $this->getState();
        }

        return $record;
    }
}
