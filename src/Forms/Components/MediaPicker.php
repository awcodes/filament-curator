<?php

namespace FilamentCurator\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Support\Actions\Concerns;
use FilamentCurator\Actions\MediaPickerAction;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaPicker extends Field
{
    use Concerns\HasColor;
    use Concerns\HasSize;
    use Concerns\CanBeOutlined;

    protected string $view = 'filament-curator::components.media-picker';

    protected string | Htmlable | Closure | null $buttonLabel = null;

    protected string $mediaModel;

    protected bool | Closure | null $fitContent = false;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->buttonLabel = __('filament-curator::media-picker.button_label');
        $this->size = 'md';
        $this->color = 'primary';
        $this->isOutlined = true;

        $this->mediaModel = config('filament-curator.model');

        $this->registerActions([
            MediaPickerAction::make(),
            Action::make('download')->action(function (): StreamedResponse {
                $item = resolve($this->mediaModel)->where('id', $this->getState())->first();

                return Storage::disk($item['disk'])->download($item['filename']);
            }),
        ]);
    }

    public function fitContent(bool | Closure | null $fitContent = true): static
    {
        $this->fitContent = $fitContent;

        return $this;
    }

    public function buttonLabel(string | Htmlable | Closure | null $buttonLabel): static
    {
        $this->buttonLabel = $buttonLabel;

        return $this;
    }

    public function getCurrentItem(): Model | null
    {
        return resolve($this->mediaModel)->where('id', $this->getState())->first();
    }

    public function getButtonLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->buttonLabel);
    }

    public function download($state): StreamedResponse
    {
        $item = resolve($this->mediaModel)->where('id', $id)->first();

        return Storage::disk($item['disk'])->download($item['filename']);
    }

    public function getFitContent(): bool
    {
        return $this->evaluate($this->fitContent);
    }
}
