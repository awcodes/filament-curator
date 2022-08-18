<?php

namespace FilamentCurator\Forms\Components;

use Closure;
use Filament\Forms\Components\Actions\Action;
use FilamentCurator\Models\Media;
use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Htmlable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaPicker extends Field
{
    protected string $view = 'filament-curator::components.media-picker';

    protected string | Htmlable | Closure | null $buttonLabel = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buttonLabel = __('filament-curator::media-picker.button_label');

        // $this->registerActions([
        //     Action::make('download')->action(function($test): StreamedResponse {
        //         ray($test);
        //         $item = resolve(config('filament-curator.model'))->where('id', $id)->first();
        //         return Storage::disk($item['disk'])->download($item['filename']);
        //     })
        // ]);
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

    public function download(int $id): StreamedResponse
    {
        $item = resolve(config('filament-curator.model'))->where('id', $id)->first();
        return Storage::disk($item['disk'])->download($item['filename']);
    }
}
