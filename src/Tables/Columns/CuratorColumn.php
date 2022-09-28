<?php

namespace FilamentCurator\Tables\Columns;

use Filament\Tables\Columns\ImageColumn;

class CuratorColumn extends ImageColumn
{
    protected string $view = 'filament-curator::tables.columns.curator-column';

    public function getImagePath(): ?string
    {
        $state = $this->getState();

        if (! $state) {
            return null;
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

    public function getType(): ?string
    {
        return $this->record->image ? $this->record->image->type : '';
    }
}