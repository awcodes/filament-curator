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

    public $data;

    public $public_id;
    public $filename;
    public $ext;
    public $type;
    public $width;
    public $height;
    public $disk;
    public $size;
    public $upload;
    public $alt;
    public $title;
    public $caption;
    public $description;

    public function mount()
    {
        $this->form->fill();
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Group::make()
                ->schema([
                    MediaUpload::make('filename')
                        ->label('File')
                        ->preserveFilenames(config('filament-curator.preserve_file_names'))
                        ->maxWidth(config('filament-curator.max_width'))
                        ->minSize(config('filament-curator.min_size'))
                        ->maxSize(config('filament-curator.max_size'))
                        ->rules(config('filament-curator.rules'))
                        ->acceptedFileTypes(config('filament-curator.accepted_file_types'))
                        ->directory(config('filament-curator.directory', 'images'))
                        ->disk(config('filament-curator.disk', 'public'))
                        ->required()
                        ->maxFiles(1)
                        ->panelAspectRatio('16:9')
                        ->columnSpan(['md' => 1, 'lg' => 2]),
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('alt')
                                ->label('Alt Text')
                                ->helperText('<span class="block -mt-1 text-xs"><a href="https://www.w3.org/WAI/tutorials/images/decision-tree" target="_blank" rel="noopener" class="underline text-primary-500 hover:text-primary-600 focus:text-primary-600">Learn how to describe the purpose of the image</a>. Leave empty if the image is purely decorative.</span>'),
                            Forms\Components\TextInput::make('title'),
                            Forms\Components\Textarea::make('caption')
                                ->rows(2),
                            Forms\Components\Textarea::make('description')
                                ->rows(2),
                        ])
                        ->columnSpan(['md' => 1]),
                ])
                ->columns(['md' => 2, 'lg' => 3])
        ];
    }

    public function create(): void
    {
        $media = Media::create($this->form->getState());
        $this->form->fill([]);
        $this->dispatchBrowserEvent('new-media-added', ['media' => $media]);
    }

    public function render()
    {
        return view('filament-curator::components.create-media-form');
    }
}
