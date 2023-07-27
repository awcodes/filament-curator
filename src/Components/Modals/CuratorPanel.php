<?php

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Awcodes\Curator\Resources\MediaResource;
use Exception;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorPanel extends Component implements HasForms
{
    use WithPagination;
    use InteractsWithForms;

    public array $acceptedFileTypes = [];

    public array $data = [];

    public string|null $directory;

    public string $diskName = 'public';

    public Collection|null $files = null;

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

    public array $types = [];

    public array $validationRules = [];

    public string $visibility = 'public';

    public function mount(): void
    {
        $this->files = $this->getMedia();
        $this->addMediaForm->fill();
        $this->editMediaForm->fill();
    }

    public function addFiles(): void
    {
        $media = [];

        foreach ($this->addMediaForm->getState()['files'] as $item) {
            // Fix malformed utf-8 characters
            if (!empty($item['exif'])) {
                array_walk_recursive($item['exif'], function (&$entry) {
                    if (!mb_detect_encoding($entry, 'utf-8', true)) {
                        $entry = utf8_encode($entry);
                    }
                });
            }

            $media[] = App::make(Media::class)->create($item);
        }

        $this->addMediaForm->fill();
        $this->dispatch('new-media-added', ['media' => $media]);
    }

    public function destroyFile(): void
    {
        try {
            $item = App::make(Media::class)->find(Arr::first($this->selected)['id']);
            if ($item) {
                $item->update($this->editMediaForm->getState());
                $this->editMediaForm->fill();
                $this->dispatch('remove-media', ['media' => $item]);
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
        $item = App::make(Media::class)->where('id', Arr::first($this->selected)['id'])->first();

        return Storage::disk($item['disk'])->download($item['path']);
    }

    protected function getAddMediaFormSchema(): array
    {
        return [
            Uploader::make('files')
                ->hiddenLabel()
                ->required()
                ->multiple()
                ->label(__('curator::forms.fields.file'))
                ->panelAspectRatio('4:3')
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
        return App::make(MediaResource::class)->getAdditionalInformationFormSchema();
    }

    protected function getForms(): array
    {
        return [
            'addMediaForm' => $this->makeForm()
                ->schema($this->getAddMediaFormSchema())
                ->statePath('data'),
            'editMediaForm' => $this->makeForm()
                ->schema($this->getEditMediaFormSchema())
                ->statePath('data'),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function getMedia(): Collection
    {
        return App::make(Media::class)
            ->when($this->selected, function ($query, $selected) {
                return $query->whereNotIn('id', $selected);
            })
            ->when($this->isLimitedToDirectory, function ($query) {
                return $query->where('directory', $this->directory);
            })
            ->when($this->types, function ($query) {
                $types = $this->types;
                $query = $query->whereIn('type', $types);
                $wildcardTypes = collect($types)->filter(fn($type) => str_contains($type, '*'));
                $wildcardTypes?->map(fn($type) => $query->orWhere('type', 'LIKE', str_replace('*', '%', $type)));

                return $query;
            })
            ->latest()
            ->limit(25)
            ->offset(0)
            ->get();

//        if ($this->selected && ! $request->has('page')) {
//            $mediaModel::whereIn('id', $selected)
//                ->get()
//                ->sortBy(function ($model) use ($selected) {
//                    return array_search($model->id, $selected);
//                })
//                ->reverse()
//                ->map(function ($item) use ($files) {
//                    $files->prepend($item);
//                });
//        }
    }

    public function setSelection(array $media): void
    {
        if (count($media) === 1) {
            $item = App::make(Media::class)->find($media[0]['id']);
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
            $item = App::make(Media::class)->find(Arr::first($this->selected)['id']);
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

    public function render(): View
    {
        return view('curator::components.modals.curator-panel');
    }
}
