<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Concerns\HasCurationPresets;
use Awcodes\Curator\Models\Media;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\HasSize;
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

    protected string|Htmlable|Closure|null $buttonLabel = null;

    protected array|Closure|null $formats = null;

    protected string $view = 'curator::components.forms.curation';

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->buttonLabel(trans('curator::views.picker.button'))
            ->size('md')
            ->color('primary')
            ->outlined()
            // The persisted curation may hold a stale/expiring URL (e.g. a private
            // disk's temporary URL). Re-resolve it from disk + path on hydration so
            // the form preview is always valid, regardless of how long it's been.
            ->afterStateHydrated(static function (CuratorEditor $component, mixed $state): void {
                if (! is_array($state) || blank($state['disk'] ?? null) || blank($state['path'] ?? null)) {
                    return;
                }

                $state['url'] = Media::resolveUrl($state['disk'], $state['path'], $state['visibility'] ?? null);

                $component->state($state);
            });

        $this->registerActions([
            fn (CuratorEditor $component): Action => $component->getCurationAction(),
        ]);
    }

    public function buttonLabel(string|Htmlable|Closure|null $label): static
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function formats(array|Closure $formats): static
    {
        $this->formats = $formats;

        return $this;
    }

    public function getButtonLabel(): string|Htmlable|null
    {
        return $this->evaluate($this->buttonLabel);
    }

    public function getFormats(): array
    {
        return $this->evaluate($this->formats) ?? config('curator.curation_formats');
    }

    public function getCurationAction(): Action
    {
        return Action::make('open_curation_panel')
            ->label($this->getButtonLabel())
            ->button()
            ->color($this->getColor())
            ->outlined($this->isOutlined())
            ->size($this->getSize())
            ->modalCloseButton(false)
            ->modalWidth('screen')
            ->modalFooterActions(fn (): array => [])->modalHeading(static fn (CuratorEditor $component): string => trans('curator::views.curation.heading').' '.$component->getRecord()->name)
            ->modalContent(static fn (CuratorEditor $component, Component $livewire) => View::make('curator::components.actions.curation-action', [
                'statePath' => $component->getStatePath(),
                'modalId' => 'fi-'.$livewire->getId().'-action-0',
                'media' => $component->getRecord(),
                'presets' => $component->getPresets(),
                'formats' => $component->getFormats(),
            ]));
    }
}
