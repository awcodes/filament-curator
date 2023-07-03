<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Actions\Action;

class PickerAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'curator_picker';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth = 'screen';

        $this->modalActions([]);

        $this->modalHeading(__('curator::views.panel.heading'));

        $this->modalContent(static function (CuratorPicker $component) {
            return view('curator::components.actions.picker-action', [
                'statePath' => $component->getStatePath(),
                'modalId' => $component->getLivewire()->id.'-form-component-action',
                'directory' => $component->getDirectory(),
                'pathGenerator' => $component->getPathGenerator(),
                'shouldPreserveFilenames' => $component->shouldPreserveFilenames(),
                'maxWidth' => $component->getMaxWidth(),
                'minSize' => $component->getMinSize(),
                'maxSize' => $component->getMaxSize(),
                'rules' => $component->getValidationRules(),
                'acceptedFileTypes' => $component->getAcceptedFileTypes(),
                'diskName' => $component->getDiskName(),
                'visibility' => $component->getVisibility(),
                'imageCropAspectRatio' => $component->getImageCropAspectRatio(),
                'imageResizeMode' => $component->getImageResizeMode(),
                'imageResizeTargetWidth' => $component->getImageResizeTargetWidth(),
                'imageResizeTargetHeight' => $component->getImageResizeTargetHeight(),
                'selected' => array_values((array)$component->getState()),
                'isMultiple' => $component->isMultiple(),
                'isLimitedToDirectory' => $component->isLimitedToDirectory(),
            ]);
        });
    }
}
