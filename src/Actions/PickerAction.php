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
                'acceptedFileTypes' => $component->getAcceptedFileTypes(),
                'directory' => $component->getDirectory(),
                'diskName' => $component->getDiskName(),
                'imageCropAspectRatio' => $component->getImageCropAspectRatio(),
                'imageResizeMode' => $component->getImageResizeMode(),
                'imageResizeTargetWidth' => $component->getImageResizeTargetWidth(),
                'imageResizeTargetHeight' => $component->getImageResizeTargetHeight(),
                'isLimitedToDirectory' => $component->isLimitedToDirectory(),
                'isMultiple' => $component->isMultiple(),
                'maxItems' => $component->getMaxItems(),
                'maxSize' => $component->getMaxSize(),
                'maxWidth' => $component->getMaxWidth(),
                'minSize' => $component->getMinSize(),
                'modalId' => $component->getLivewire()->id.'-form-component-action',
                'statePath' => $component->getStatePath(),
                'pathGenerator' => $component->getPathGenerator(),
                'rules' => $component->getValidationRules(),
                'selected' => array_values((array)$component->getState()),
                'shouldPreserveFilenames' => $component->shouldPreserveFilenames(),
                'visibility' => $component->getVisibility(),
            ]);
        });
    }
}
