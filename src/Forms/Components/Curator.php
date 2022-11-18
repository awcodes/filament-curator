<?php

namespace FilamentCurator\Forms\Components;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Curator extends Component implements HasForms
{
    use InteractsWithForms;

    public $selected = null;

    public $data;

    public $statePath;

    public $modalId;

    public $state = null;

    public function mount()
    {
        $this->addMediaForm->fill();
        $this->editMediaForm->fill();
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getAddMediaFormSchema(): array
    {
        return [
            MediaUpload::make('files')
                ->label(__('filament-curator::media-form.labels.file'))
                ->disableLabel()
                ->preserveFilenames(config('filament-curator.preserve_file_names'))
                ->maxWidth(config('filament-curator.max_width'))
                ->minSize(config('filament-curator.min_size'))
                ->maxSize(config('filament-curator.max_size'))
                ->rules(config('filament-curator.rules'))
                ->acceptedFileTypes(config('filament-curator.accepted_file_types'))
                ->disk(config('filament-curator.disk', 'public'))
                ->visibility(config('filament-curator.visibility', 'public'))
                ->required()
                ->multiple(),
        ];
    }

    protected function getEditMediaFormSchema(): array
    {
        return app(config('filament-curator.media_resource'))::getAdditionInformationFormSchema();
    }

    protected function getForms(): array
    {
        return [
            'addMediaForm' => $this->makeForm()
                ->schema($this->getAddMediaFormSchema()),
            'editMediaForm' => $this->makeForm()
                ->schema($this->getEditMediaFormSchema()),
        ];
    }

    public function download(): StreamedResponse
    {
        $item = resolve(config('filament-curator.model'))->where('id', $this->selected['id'])->first();

        return Storage::disk($item['disk'])->download($item['filename']);
    }

    public function setCurrentFile(array | null $media): void
    {
        if ($media) {
            $item = resolve(config('filament-curator.model'))->firstWhere('id', $media['id']);
            if ($item) {
                $this->editMediaForm->fill([
                    'alt' => $item->alt,
                    'title' => $item->title,
                    'caption' => $item->caption,
                    'description' => $item->description,
                ]);
            }
            $this->selected = $item;
        } else {
            $this->editMediaForm->fill();
            $this->selected = null;
        }
    }

    public function addFiles(): void
    {
        $media = [];

        foreach ($this->addMediaForm->getState()['files'] as $item) {
            $media[] = resolve(config('filament-curator.model'))->create($item);
        }

        $this->addMediaForm->fill();
        $this->dispatchBrowserEvent('new-media-added', ['media' => $media]);
    }

    public function updateFile(): void
    {
        $this->selected->update($this->editMediaForm->getState());
    }

    public function destroyFile(): void
    {
        $this->editMediaForm->fill();
        $this->dispatchBrowserEvent('remove-media', ['media' => $this->selected]);
        $this->selected->delete();
        $this->selected = null;
    }

    public function insertMedia(): void
    {
        $this->dispatchBrowserEvent('insert-media', ['media' => $this->selected, 'statePath' => $this->statePath]);
    }

    public function render()
    {
        return view('filament-curator::components.curator');
    }
}
