<?php

namespace FilamentCurator\Forms\Components;

use Closure;
use FilamentCurator\Models\Media;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Htmlable;

class MediaPicker extends Field
{
    protected string $view = 'filament-curator::components.media-picker';

    protected string | Htmlable | Closure | null $buttonLabel = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buttonLabel = __('filament-curator::media-picker.button_label');
    }

    public function buttonLabel(string | Htmlable | Closure | null $buttonLabel): static
    {
        $this->buttonLabel = $buttonLabel;

        return $this;
    }

    public function getCurrentItem($state)
    {
        return resolve(config('filament-curator.model'))->where('id', $state)->first();
    }

    public function getButtonLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->buttonLabel);
    }
}
