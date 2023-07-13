<?php

namespace Awcodes\Curator\Resources\MediaResource;

use Awcodes\Curator\CuratorPlugin;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    public static function getResource(): string
    {
        return CuratorPlugin::get()->getResource();
    }

    /**
     * @throws Exception
     */
    public function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->action('save')
                ->label(__('curator::views.panel.edit_save')),
            Action::make('preview')
                ->color('gray')
                ->url($this->record->url, shouldOpenInNewTab: true)
                ->label(__('curator::views.panel.view')),
            DeleteAction::make(),
        ];
    }
}
