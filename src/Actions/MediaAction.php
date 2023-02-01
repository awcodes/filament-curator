<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Actions\Action;
use FilamentTiptapEditor\TiptapEditor;

class MediaAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'filament_tiptap_media';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth('screen');

        $this->modalHeading(__('curator::views.panel.heading'));

        $this->modalActions(fn () => []);

        $this->modalContent(static function (TiptapEditor|CuratorPicker $component) {
            return view('curator::components.actions.picker-action', [
                'statePath' => $component->getStatePath(),
                'modalId' => $component->getLivewire()->id.'-form-component-action',
                'directory' => app('curator')->getDirectory(),
                'pathGenerator' => app('curator')->getPathGenerator(),
                'shouldPreserveFilenames' => app('curator')->shouldPreserveFilenames(),
                'maxWidth' => app('curator')->getMaxWidth(),
                'minSize' => app('curator')->getMinSize(),
                'maxSize' => app('curator')->getMaxSize(),
                'rules' => [],
                'acceptedFileTypes' => app('curator')->getAcceptedFileTypes(),
                'diskName' => app('curator')->getDiskName(),
                'visibility' => app('curator')->getVisibility(),
                'imageCropAspectRatio' => app('curator')->getImageCropAspectRatio(),
                'imageResizeTargetWidth' => app('curator')->getImageResizeTargetWidth(),
                'imageResizeTargetHeight' => app('curator')->getImageResizeTargetHeight(),
            ]);
        });
    }
}
