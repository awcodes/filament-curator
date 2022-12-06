<?php


namespace FilamentCurator\Actions;

use Filament\Forms\Components\Actions\Action;
use FilamentCurator\Forms\Components\MediaPicker;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'filament_curator_download_media';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function (MediaPicker $component): StreamedResponse {
            $item = resolve(config('filament-curator.model'))->where('id', $component->getState())->first();
            return Storage::disk($item['disk'])->download($item['filename']);
        });
    }
}