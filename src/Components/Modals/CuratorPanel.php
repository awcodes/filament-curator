<?php

namespace RocketFirm\Curator\Components\Modals;

use RocketFirm\Curator\Components\Forms\Uploader;
use RocketFirm\Curator\Facades\Curator;
use RocketFirm\Curator\Generators\PathGenerator;
use RocketFirm\Curator\Resources\MediaResource;
use Exception;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorPanel extends Component implements HasForms
{
    use InteractsWithForms;

    public array $acceptedFileTypes = [];

    public array $data = [];

    public string|null $directory;

    public string $diskName = 'public';

    public string|null $imageCropAspectRatio = null;

    public string|null $imageResizeMode = null;

    public string|null $imageResizeTargetWidth = null;

    public string|null $imageResizeTargetHeight = null;

    public bool $isLimitedToDirectory = false;

    public bool $isMultiple = false;

    public int|null $maxSize = null;

    public int|null $maxWidth = null;

    public int|null $minSize = null;

    public int|null $mediaId = null;

    public string $modalId;

    public PathGenerator|string|null $pathGenerator = null;

    public array $selected = [];

    public string|null $statePath;

    public bool $shouldPreserveFilenames = false;

    public array $validationRules = [];

    public string $visibility = 'public';

    public function addFiles(): void
    {
        $media = [];

        foreach ($this->addMediaForm->getState()['files'] as $item) {
            // Fix malformed utf-8 characters
            /*if (! empty($item['exif'])) {
                array_walk_recursive($item['exif'], function (&$entry) {
                    if (! mb_detect_encoding($entry, 'utf-8', true)) {
                        $entry = utf8_encode($entry);
                    }
                });
            }*/
            $item['exif'] = null;

            $media[] = Curator::getMediaModel()::create($item);
        }

        $this->addMediaForm->fill();
        $this->dispatchBrowserEvent('new-media-added', ['media' => $media]);
    }

    public function destroyFile(): void
    {
        try {
            $item = Curator::getMediaModel()::find(Arr::first($this->selected)['id']);
            if ($item) {
                $item->update($this->editMediaForm->getState());
                $this->editMediaForm->fill();
                $this->dispatchBrowserEvent('remove-media', ['media' => $item]);
                $item->delete();
                $this->selected = [];

                Notification::make('curator_delete_success')
                    ->success()
                    ->body(__('curator::notifications.delete_success'))
                    ->send();
            } else {
                throw new Exception();
            }
        } catch (Exception) {
            Notification::make('curator_delete_error')
                ->danger()
                ->body(__('curator::notifications.delete_error'))
                ->send();
        }
    }

    public function download(): StreamedResponse
    {
        $item = Curator::getMediaModel()::where('id', $this->selected['id'])->first();

        return Storage::disk($item['disk'])->download($item['path']);
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
                ->imageResizeMode($this->imageResizeMode)
                ->imageResizeTargetWidth($this->imageResizeTargetWidth)
                ->imageResizeTargetHeight($this->imageResizeTargetHeight),
        ];
    }

    protected function getEditMediaFormSchema(): array
    {
        return app(MediaResource::class)->getAdditionalInformationFormSchema();
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

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function mount(): void
    {
        $this->addMediaForm->fill();
        $this->editMediaForm->fill();
    }

    public function render(): View
    {
        return view('curator::components.modals.curator-panel');
    }

    public function setSelection(array $media): void
    {
        if (count($media) === 1) {
            $item = Curator::getMediaModel()::find($media[0]['id']);
            if ($item) {
                $this->editMediaForm->fill([
                    'name' => $item->name,
                    'alt' => $item->alt,
                    'title' => $item->title,
                    'caption' => $item->caption,
                    'description' => $item->description,
                ]);
            }
            $this->selected = [$item];
        } else {
            $this->editMediaForm->fill();
            $this->selected = [];
        }
    }

    public function updateFile(): void
    {
        try {
            $item = Curator::getMediaModel()::find(Arr::first($this->selected)['id']);
            if ($item) {
                $item->update($this->editMediaForm->getState());

                Notification::make('curator_update_success')
                    ->success()
                    ->body(__('curator::notifications.update_success'))
                    ->send();
            } else {
                throw new Exception();
            }
        } catch (Exception) {
            Notification::make('curator_update_error')
                ->danger()
                ->body(__('curator::notifications.update_error'))
                ->send();
        }
    }
}
