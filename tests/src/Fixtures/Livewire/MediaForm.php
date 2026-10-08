<?php

namespace Awcodes\Curator\Tests\Fixtures\Livewire;

use Awcodes\Curator\Actions\MediaAction;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use FilamentTiptapEditor\TiptapEditor;
use Livewire\Component;

class MediaForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                CuratorPicker::make('media')
                    ->acceptedFileTypes(['image/png'])
                    ->directory('pictures')
                    ->multiple(),
                TiptapEditor::make('content')
                    ->mediaAction(MediaAction::class)
                    ->acceptedFileTypes(['image/jpeg'])
                    ->directory('editor'),
            ])
            ->statePath('data');
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>{{ $this->form }}</div>
            BLADE;
    }
}
