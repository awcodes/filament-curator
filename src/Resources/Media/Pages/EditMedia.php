<?php

declare(strict_types=1);

namespace Awcodes\Curator\Resources\Media\Pages;

use Awcodes\Curator\Resources\Media\MediaResource;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    /** @throws Exception */
    public function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->action('save')
                ->label(trans('curator::views.panel.edit_save')),
            Action::make('preview')
                ->color('gray')
                ->url($this->record->url, shouldOpenInNewTab: true)
                ->label(trans('curator::views.panel.view')),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $state = $this->getSchema('form')->getRawState();

        if ($state['file'] !== null) {
            $livewire = $this->getSchema('form')->getLivewire();
            $statePath = $this->getSchema('form')->getStatePath();

            data_set($livewire, $statePath.'.file', null);
        }
    }
}
