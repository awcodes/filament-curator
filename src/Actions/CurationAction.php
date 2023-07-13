<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\CuratorEditor;
use Filament\Forms\Components\Actions\Action;
use Livewire\Component;

class CurationAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'curator_curation';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth = 'screen';

        $this->modalFooterActions(fn() => []);

        $this->modalHeading(static function (CuratorEditor $component) {
            return __('curator::views.curation.heading') . ' ' . $component->getRecord()->name;
        });

        $this->modalContent(static function (CuratorEditor $component, Component $livewire) {
            return view('curator::components.actions.curation-action', [
                'statePath' => $component->getStatePath(),
                'modalId' => $livewire->id . '-form-component-action',
                'media' => $component->getRecord(),
                'presets' => $component->getPresets(),
            ]);
        });
    }
}
