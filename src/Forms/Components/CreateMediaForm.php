<?php

namespace FilamentCurator\Forms\Components;

use Filament\Forms;
use Livewire\Component;
use FilamentCurator\Models\Media;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Actions\Concerns\CanSubmitForm;

class CreateMediaForm extends Component implements HasForms
{
    use InteractsWithForms;

    public array $data = [];

    public string $public_id = '';
    public string $filename = '';
    public string $ext = '';
    public string $type = '';
    public int $width = 0;
    public int $height = 0;
    public string $disk = '';
    public int $size = 0;
    public string $alt = '';
    public string $title = '';
    public string $caption = '';
    public string $description = '';

    public function mount()
    {
        $this->form->fill([]);
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
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
                ->directory(config('filament-curator.directory', 'images'))
                ->disk(config('filament-curator.disk', 'public'))
                ->visibility(config('filament-curator.visibility', 'public'))
                ->required()
                ->multiple()
                ->panelLayout('integrated'),
        ];
    }

    public function create(): void
    {
        $media = [];
        foreach ($this->form->getState()['files'] as $item) {
            $media[] = resolve(config('filament-curator.model'))->create($item);
        }

        $this->form->fill([]);
        $this->dispatchBrowserEvent('new-media-added', ['media' => $media]);
    }

    public function render()
    {
        return view('filament-curator::components.create-media-form');
    }
}
