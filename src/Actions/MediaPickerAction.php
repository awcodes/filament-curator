<?php


namespace FilamentCurator\Actions;

use Closure;
use Filament\Forms\Components\Actions\Action;
use FilamentCurator\Forms\Components\MediaPicker;
use Illuminate\Contracts\Support\Htmlable;
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

        $this->modalWidth = 'screen';

        $this->modalActions([]);

        $this->modalHeading(__('filament-curator::media-picker-modal.heading'));

        $this->modalContent(static function(MediaPicker $component): View {
            return view('filament-curator::components.media-action', [
                'statePath' => $component->getStatePath(),
                'modalId' => $component->getLivewire()->id . '-form-component-action',
                'directory' => $component->getDirectory(),
                'shouldPreserveFilenames' => $component->shouldPreserveFilenames(),
                'maxWidth' => $component->getMaxWidth(),
                'minSize' => $component->getMinSize(),
                'maxSize' => $component->getMaxSize(),
                'rules' => $component->getValidationRules(),
                'acceptedFileTypes' => $component->getAcceptedFileTypes(),
                'diskName' => $component->getDiskName(),
                'visibility' => $component->getVisibility(),
                'imageCropAspectRatio' => $component->getImageCropAspectRatio(),
                'imageResizeTargetWidth' => $component->getImageResizeTargetWidth(),
                'imageResizeTargetHeight' => $component->getImageResizeTargetHeight(),
            ]);
        });
    }
}