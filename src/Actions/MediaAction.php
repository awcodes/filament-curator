<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Models\Media;
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

        $this->modalFooterActions(fn () => []);

        $this->modalContent(static function (Scribe | CuratorPicker $component) {

            $src = $component->getLivewire()->mediaProps['src'];
            $selected = $src !== ''
                ? [app(Media::class)->firstWhere('name', Str::of($src)->afterLast('/')->beforeLast('.'))]
                : [];

            return view('curator::components.actions.picker-action', [
                'acceptedFileTypes' => config('curator.accepted_file_types'),
                'directory' => config('curator.directory'),
                'diskName' => config('curator.disk'),
                'imageCropAspectRatio' => config('curator.image_crop_aspect_ratio'),
                'imageResizeTargetWidth' => config('curator.image_resize_target_width'),
                'imageResizeTargetHeight' => config('curator.image_resize_target_height'),
                'imageResizeMode' => config('curator.image_resize_mode'),
                'isLimitedToDirectory' => false,
                'isMultiple' => false,
                'maxItems' => 1,
                'maxSize' => config('curator.max_size'),
                'maxWidth' => config('curator.max_width'),
                'minSize' => config('curator.min_size'),
                'modalId' => $component->getLivewire()->id . '-form-component-action',
                'pathGenerator' => config('curator.path_generator'),
                'rules' => [],
                'selected' => $selected,
                'shouldPreserveFilenames' => config('curator.should_preserve_filenames'),
                'statePath' => $component->getStatePath(),
                'visibility' => config('curator.visibility'),
            ]);
        });
    }
}
