<?php


namespace FilamentCurator\Actions;

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

        $this->modalHeading(__('filament-curator::media-picker-modal.heading'));

        $this->modalActions(fn () => []);

        $this->modalContent(function() {
            return view('filament-curator::components.curator');
        });

//        $this->action(function (TiptapEditor $component, $data) {
//            dd('test curator action');
////            $component->getLivewire()->dispatchBrowserEvent('insert-media', [
////                'statePath' => $component->getStatePath(),
////                'src' => $data['src'],
////                'alt' => $data['alt'] ?? null,
////                'title' => $data['title'],
////                'width' => $data['width'],
////                'height' => $data['height'],
////                'link_text' => $data['link_text'] ?? null,
////            ]);
//        });
    }
}