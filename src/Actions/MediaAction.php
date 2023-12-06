<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Models\Media;
use Filament\Forms\Components\Actions\Action;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Livewire\Component;

class MediaAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'filament_tiptap_media';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->arguments([
                'src' => '',
            ])
            ->action(function (TiptapEditor|CuratorPicker $component, Component $livewire, array $arguments) {

                $selected = $arguments['src'] !== ''
                    ? [App::get(Media::class)->firstWhere('name', Str::of($arguments['src'])->afterLast('/')->beforeLast
                    ('.'))]
                    : [];

                $livewire->dispatch('open-modal', id: 'curator-panel', settings: [
                    'acceptedFileTypes' => Config::get('curator.accepted_file_types'),
                    'directory' => Config::get('curator.directory'),
                    'diskName' => Config::get('curator.disk'),
                    'imageCropAspectRatio' => Config::get('curator.image_crop_aspect_ratio'),
                    'imageResizeTargetWidth' => Config::get('curator.image_resize_target_width'),
                    'imageResizeTargetHeight' => Config::get('curator.image_resize_target_height'),
                    'imageResizeMode' => Config::get('curator.image_resize_mode'),
                    'isLimitedToDirectory' => false,
                    'isMultiple' => false,
                    'maxItems' => 1,
                    'maxSize' => Config::get('curator.max_size'),
                    'maxWidth' => Config::get('curator.max_width'),
                    'minSize' => Config::get('curator.min_size'),
                    'modalId' => $component->getLivewire()->getId() . '-form-component-action',
                    'pathGenerator' => Config::get('curator.path_generator'),
                    'rules' => [],
                    'selected' => $selected,
                    'shouldPreserveFilenames' => Config::get('curator.should_preserve_filenames'),
                    'statePath' => $component->getStatePath(),
                    'types' => $component->getAcceptedFileTypes(),
                    'visibility' => Config::get('curator.visibility'),
                ]);
            });
    }
}
