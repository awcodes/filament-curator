<?php


namespace FilamentCurator\Actions;

use Filament\Forms\Components\Actions\Action;
use FilamentCurator\Forms\Components\MediaPicker;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\View\View;

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

        $this->modalHeading(__('filament-curator::media-picker-modal.heading'));

        $this->modalActions(fn () => []);

        $this->modalContent(static function(TiptapEditor | MediaPicker $component): View {
            return view('filament-curator::components.media-action', [
                'statePath' => $component->getStatePath(),
                'modalId' => $component->getLivewire()->id . '-form-component-action',
                'directory' => config('filament-tiptap-editor.directory'),
                'shouldPreserveFilenames' => config('filament-tiptap-editor.preserve_file_names'),
                'maxWidth' => config('filament-curator.max_width'),
                'minSize' => config('filament-curator.min_size'),
                'maxSize' => config('filament-tiptap-editor.max_file_size'),
                'rules' => [],
                'acceptedFileTypes' => config('filament-tiptap-editor.accepted_file_types'),
                'diskName' => config('filament-tiptap-editor.disk'),
                'visibility' => config('filament-tiptap-editor.visibility'),
                'imageCropAspectRatio' => config('filament-tiptap-editor.image_crop_aspect_ratio'),
                'imageResizeTargetWidth' => config('filament-tiptap-editor.image_resize_target_width'),
                'imageResizeTargetHeight' => config('filament-tiptap-editor.image_resize_target_height'),
            ]);
        });
    }
}