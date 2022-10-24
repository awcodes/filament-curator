<?php

namespace FilamentCurator\Forms\Components;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaPickerModal extends Component implements HasForms
{
    use InteractsWithForms;

    public array | null $selected = null;

    public array $data;

    public function updatedSelected($value): void
    {
        if ($value) {
            $item = resolve(config('filament-curator.model'))->firstWhere('id', $value['id']);
            if ($item) {
                $this->data = [
                    'alt' => $item->alt,
                    'title' => $item->title,
                    'caption' => $item->caption,
                    'description' => $item->description,
                ];
            }
        }
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('alt')
                ->label(__('filament-curator::media-form.labels.alt'))
                ->extraInputAttributes(['aria-describedby' => 'edit-alt-helper']),
            Forms\Components\View::make('filament-curator::alt-helper')
                ->extraAttributes(['id' => 'edit-alt-helper']),
            Forms\Components\TextInput::make('title')
                ->label(__('filament-curator::media-form.labels.title')),
            Forms\Components\Textarea::make('caption')
                ->label(__('filament-curator::media-form.labels.caption'))
                ->rows(2),
            Forms\Components\Textarea::make('description')
                ->label(__('filament-curator::media-form.labels.description'))
                ->rows(2),
        ];
    }

    public function download(): StreamedResponse
    {
        $item = resolve(config('filament-curator.model'))->where('id', $this->selected['id'])->first();

        return Storage::disk($item['disk'])->download($item['filename']);
    }

    public function update(): void
    {
        $item = resolve(config('filament-curator.model'))->where('id', $this->selected['id'])->first();
        Filament::notify('success', __('filament-curator::media-form.notices.success'));
        $item->update($this->data);
    }

    public function destroy(): void
    {
        $item = resolve(config('filament-curator.model'))->where('id', $this->selected['id'])->first();
        $this->data = [];
        $this->selected = null;
        $this->dispatchBrowserEvent('remove-media', ['media' => $item]);
        $item->delete();
    }

    public function render()
    {
        return view('filament-curator::components.media-picker-modal');
    }
}
