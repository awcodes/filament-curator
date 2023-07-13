<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Actions\CurationAction;
use Awcodes\Curator\Concerns\HasCurationPresets;
use Closure;
use Exception;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\HasSize;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasColor;
use Illuminate\Contracts\Support\Htmlable;

class CuratorEditor extends Field
{
    use CanBeOutlined;
    use HasColor;
    use HasSize;
    use HasCurationPresets;

    protected string | Htmlable | Closure | null $buttonLabel = null;

    protected string $view = 'curator::components.forms.curation';

    public function buttonLabel(string | Htmlable | Closure | null $label): static
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function getButtonLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->buttonLabel);
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->buttonLabel = __('curator::views.picker.button');
        $this->size = 'md';
        $this->color = 'primary';
        $this->isOutlined = true;

        $this->registerActions([
            CurationAction::make(),
        ]);
    }
}
