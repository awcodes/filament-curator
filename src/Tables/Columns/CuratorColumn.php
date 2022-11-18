<?php

namespace FilamentCurator\Tables\Columns;

use Filament\Tables\Columns\ImageColumn;
use FilamentCurator\Facades\CuratorThumbnails;
use Illuminate\Support\Str;

class CuratorColumn extends ImageColumn
{
    protected string $view = 'filament-curator::tables.columns.curator-column';

    public function getImagePath(): ?string
    {
        $state = $this->getState();

        if (! $state) {
            return null;
        }

        if (is_string($state)) {
            return $state;
        }

        /** @var FilesystemAdapter $storage */
        $storage = $this->getDisk();

        if (! $storage->exists($state->filename)) {
            return null;
        }

        if ($this->getVisibility() === 'private' || $storage->getVisibility($state->filename) === 'private') {
            try {
                return $storage->temporaryUrl(
                    $state->filename,
                    now()->addMinutes(5),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $state->thumbnail_url;
    }

    public function isImage(): bool
    {
        $state = $this->getState();

        if (filled($state)) {
            if (is_a($state, config('filament-curator.model'))) {
                $url = $state->filename;
            } else {
                $url = $state;
            }

            $ext = Str::of($url)->afterLast('.');

            return CuratorThumbnails::isResizable($ext);
        }

        return false;
    }
}
