<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Facades\Curator;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use FilamentTiptapEditor\TiptapEditor;
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

        $this->mountUsing(function (TiptapEditor|CuratorPicker $component, ComponentContainer $form) {
            $src = $component->getLivewire()->mediaProps['src'];
            $media = $src !== ''
                ? Curator::getMediaModel()::firstWhere('name', Str::of($src)->afterLast('/')->beforeLast('.'))
                : null;

//            ray($component->getLivewire()->mediaProps);

//            $form->fill([
//                'src' => $source,
//                'alt' => $component->getLivewire()->mediaProps['alt'],
//                'title' => $component->getLivewire()->mediaProps['title'],
//                'width' => $component->getLivewire()->mediaProps['width'],
//                'height' => $component->getLivewire()->mediaProps['height'],
//            ]);
        });

        $this->modalWidth('screen');

        $this->modalHeading(__('curator::views.panel.heading'));

        $this->modalActions(fn () => []);

        $this->modalContent(static function (TiptapEditor|CuratorPicker $component) {
            $src = $component->getLivewire()->mediaProps['src'];
            $selected = $src !== ''
                ? Curator::getMediaModel()::firstWhere('name', Str::of($src)->afterLast('/')->beforeLast('.'))
                : null;
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
                'selected' => $selected
            ]);
        });
    }
}
