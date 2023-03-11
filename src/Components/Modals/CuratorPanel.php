<?php

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\MediaResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorPanel extends Component implements HasForms
{
    use InteractsWithForms;

    public $selected = null;

    public $data;

    public $statePath;

    public $modalId;

    public $state = null;

    public $directory;

    public $pathGenerator;

    public $shouldPreserveFilenames;

    public $maxWidth;

    public $minSize;

    public $maxSize;

    public $validationRules = [];

    public $acceptedFileTypes = [];

    public $diskName;

    public $visibility;

    public $imageCropAspectRatio;

    public $imageResizeTargetWidth;

    public $imageResizeTargetHeight;

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
            Uploader::make('files')
                ->disableLabel()
                ->required()
                ->multiple()
                ->label(__('curator::forms.fields.file'))
                ->preserveFilenames($this->shouldPreserveFilenames)
                ->maxWidth($this->maxWidth)
                ->minSize($this->minSize)
                ->maxSize($this->maxSize)
                ->rules($this->validationRules)
                ->acceptedFileTypes($this->acceptedFileTypes)
                ->disk($this->diskName)
                ->visibility($this->visibility)
                ->directory($this->directory)
                ->pathGenerator($this->pathGenerator)
                ->imageCropAspectRatio($this->imageCropAspectRatio)
                ->imageResizeTargetWidth($this->imageResizeTargetWidth)
                ->imageResizeTargetHeight($this->imageResizeTargetHeight),
        ];
    }

    protected function getEditMediaFormSchema(): array
    {
        return MediaResource::getAdditionalInformationFormSchema();
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
        $item = Media::where('id', $this->selected['id'])->first();

        return Storage::disk($item['disk'])->download($item['path']);
    }

    public function setCurrentFile(array|null $media): void
    {
        if ($media) {
            $item = Media::firstWhere('id', $media['id']);
            if ($item) {
                $this->editMediaForm->fill([
                    'name' => $item->name,
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
            // Fix malformed utf-8 characters
            if (!empty($item['exif'])) {
                array_walk_recursive($item['exif'], function(&$entry){
                    if(!mb_detect_encoding($entry, 'utf-8', true)){
                        $entry = utf8_encode($entry);
                    }
                });
            }

            $media[] = Media::create($item);
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

    public function render(): View
    {
        return view('curator::components.modals.curator-panel');
    }
}
