<?php

namespace Awcodes\Curator\Resources\MediaResource;

use Awcodes\Curator\Resources\MediaResource;
use Exception;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    /**
     * @throws Exception
     */
    public function getActions(): array
    {
        return [
            Action::make('save')
                ->action('save')
                ->label(__('curator::views.panel.edit_save')),
            Action::make('preview')
                ->color('secondary')
                ->url($this->record->url, shouldOpenInNewTab: true)
                ->label(__('curator::views.panel.view')),
            DeleteAction::make(),
        ];
    }
}
