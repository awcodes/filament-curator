<?php

namespace Awcodes\Curator\Actions;

use Awcodes\Curator\Components\Forms\CuratorEditor;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\View;
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

        $this
            ->modalWidth('screen')
            ->modalFooterActions(fn() => [])->modalHeading(static function (CuratorEditor $component) {
                return __('curator::views.curation.heading') . ' ' . $component->getRecord()->name;
            })
            ->modalContent(static function (CuratorEditor $component, Component $livewire) {
                return View::make('curator::components.actions.curation-action', [
                    'statePath' => $component->getStatePath(),
                    'modalId' => $livewire->getId() . '-form-component-action',
                    'media' => $component->getRecord(),
                    'presets' => $component->getPresets(),
                ]);
            });
    }
}
