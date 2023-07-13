<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Facades\CuratorConfig;
use Awcodes\Scribe\Scribe;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Str;

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

        $this->modalFooterActions(fn() => []);

        $this->modalContent(static function (Scribe|CuratorPicker $component) {

            $src = $component->getLivewire()->mediaProps['src'];
            $selected = $src !== ''
                ? [CuratorConfig::getMediaModel()->firstWhere('name', Str::of($src)->afterLast('/')->beforeLast('.'))]
                : [];

            return view('curator::components.actions.picker-action', [
                'acceptedFileTypes' => CuratorConfig::getAcceptedFileTypes(),
                'directory' => CuratorConfig::getDirectory(),
                'diskName' => CuratorConfig::getDiskName(),
                'imageCropAspectRatio' => CuratorConfig::getImageCropAspectRatio(),
                'imageResizeTargetWidth' => CuratorConfig::getImageResizeTargetWidth(),
                'imageResizeTargetHeight' => CuratorConfig::getImageResizeTargetHeight(),
                'imageResizeMode' => CuratorConfig::getImageResizeMode(),
                'isLimitedToDirectory' => false,
                'isMultiple' => false,
                'maxItems' => 1,
                'maxSize' => CuratorConfig::getMaxSize(),
                'maxWidth' => CuratorConfig::getMaxWidth(),
                'minSize' => CuratorConfig::getMinSize(),
                'modalId' => $component->getLivewire()->id . '-form-component-action',
                'pathGenerator' => CuratorConfig::getPathGenerator(),
                'rules' => [],
                'selected' => $selected,
                'shouldPreserveFilenames' => CuratorConfig::shouldPreserveFilenames(),
                'statePath' => $component->getStatePath(),
                'visibility' => CuratorConfig::getVisibility(),
            ]);
        });
    }
}
