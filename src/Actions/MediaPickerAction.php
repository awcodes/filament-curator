<?php


namespace FilamentCurator\Actions;

use Filament\Forms\Components\Actions\Action;
use FilamentCurator\Forms\Components\MediaPicker;
use Illuminate\View\View;

class MediaPickerAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'filament_curator_media_picker';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth('screen');

        $this->modalHeading(__('filament-curator::media-picker-modal.heading'));

        $this->modalActions(fn () => []);

        $this->modalContent(fn(MediaPicker $component): View => view('filament-curator::components.media-action', [
            'statePath' => $component->getStatePath(),
            'modalId' => $component->getLivewire()->id . '-form-component-action',
        ]));
    }
}