<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Concerns\HasCurationPresets;
use Closure;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\HasSize;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasColor;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class CuratorEditor extends Field
{
    use CanBeOutlined;
    use HasColor;
    use HasCurationPresets;
    use HasSize;

    protected string | Htmlable | Closure | null $buttonLabel = null;

    protected array | Closure | null $formats = null;

    protected string $view = 'curator::components.forms.curation';

    public function buttonLabel(string | Htmlable | Closure | null $label): static
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function formats(array | Closure $formats): static
    {
        $this->formats = $formats;

        return $this;
    }

    public function getButtonLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->buttonLabel);
    }

    public function getFormats(): array
    {
        return $this->evaluate($this->formats) ?? config('curator.curation_formats');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->buttonLabel(trans('curator::views.picker.button'))
            ->size('md')
            ->color('primary')
            ->outlined();

        $this->registerActions([
            fn (CuratorEditor $component): Action => $component->getCurationAction(),
        ]);
    }

    public function getCurationAction(): Action
    {
        return Action::make('open_curation_panel')
            ->label($this->getButtonLabel())
            ->button()
            ->color($this->getColor())
            ->outlined($this->isOutlined())
            ->size($this->getSize())
            ->modalWidth('screen')
            ->modalFooterActions(fn () => [])->modalHeading(static function (CuratorEditor $component) {
                return trans('curator::views.curation.heading') . ' ' . $component->getRecord()->name;
            })
            ->modalContent(static function (CuratorEditor $component, Component $livewire) {
                return View::make('curator::components.actions.curation-action', [
                    'statePath' => $component->getStatePath(),
                    'modalId' => $livewire->getId() . '-form-component-action',
                    'media' => $component->getRecord(),
                    'presets' => $component->getPresets(),
                    'formats' => $component->getFormats(),
                ]);
            });
    }
}
