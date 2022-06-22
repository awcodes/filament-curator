<?php

namespace App\Filament\Resources\Curator\MediaResource\Pages;

use App\Filament\Resources\Curator\MediaResource;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    public function getActions(): array
    {
        return [
            Action::make('save')->action('save'),
            Action::make('view')->color('secondary')->url($this->record->url)->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
