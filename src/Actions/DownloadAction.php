<?php

namespace RocketFirm\Curator\Actions;

use RocketFirm\Curator\Components\Forms\CuratorPicker;
use RocketFirm\Curator\Facades\Curator;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'curator_download';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function (CuratorPicker $component): StreamedResponse {
            $item = Curator::getMediaModel()::where('id', $component->getState())->first();

            return Storage::disk($item['disk'])->download($item['path']);
        });
    }
}
