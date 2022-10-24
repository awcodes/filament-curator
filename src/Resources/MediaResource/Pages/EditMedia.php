<?php

namespace FilamentCurator\Resources\MediaResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use FilamentCurator\Resources\MediaResource;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    public function getActions(): array
    {
        return [
            Action::make('save')->action('save')->label(__('filament-curator::resource.labels.save')),
            Action::make('preview')->color('secondary')->url($this->record->url)->openUrlInNewTab()->label(__('filament-curator::resource.labels.preview')),
            DeleteAction::make(),
        ];
    }
}
