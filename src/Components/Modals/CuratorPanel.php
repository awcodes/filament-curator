<?php

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Awcodes\Curator\Resources\MediaResource;
use Exception;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\View as FormView;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorPanel extends Component implements HasForms, HasActions
{
    use WithPagination;
    use InteractsWithForms;
    use InteractsWithActions;

    public array $acceptedFileTypes = [];

    public string $context = 'create';

    public ?array $data = [];

    public string|null $directory;

    public string $diskName = 'public';

    public array|null $files = [];

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

    public string $search = '';

    public array $selected = [];

    public string|null $statePath;

    public bool $shouldPreserveFilenames = false;

    public array $types = [];

    public array $validationRules = [];

    public string $visibility = 'public';

    public function mount(): void
    {
        $this->form->fill();
        $this->files = $this->getFiles();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Uploader::make('files_to_add')
                    ->visible(fn () => $this->context === 'create')
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
                Group::make([
                    FormView::make('preview')
                        ->view('curator::components.forms.edit-preview'),
                    ...App::make(MediaResource::class)->getAdditionalInformationFormSchema(),
                ])->visible(fn () => $this->context === 'edit'),
            ])->statePath('data');
    }

    public function getFiles(int $offset = 0): array
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
            ->offset($offset)
            ->limit(8)
            ->get()
            ->toArray();
    }

    public function loadMoreFiles(): void
    {
        $this->files = [
            ...$this->files,
            ...$this->getFiles(count($this->files))
        ];
    }

    public function addToSelection(int $id): void
    {
        $item = collect($this->files)->firstWhere('id', $id);

        if ($this->isMultiple) {
            $this->selected[] = $item;
        } else {
            $this->selected = [$item];
        }

        $this->context = filled($this->selected) ? 'edit' : 'create';
        $this->setMediaForm();
    }

    public function removeFromSelection(int $id): void
    {
        $this->selected = collect($this->selected)->reject(function ($selectedItem) use ($id) {
            return $selectedItem['id'] === $id;
        })->toArray();
        $this->context = filled($this->selected) ? 'edit' : 'create';
    }

    public function updatedSearch(): void
    {
        $this->files = App::make(Media::class)
            ->when($this->isLimitedToDirectory, function ($query) {
                return $query->where('directory', $this->directory);
            })
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('alt', 'like', '%' . $this->search . '%')
            ->orWhere('caption', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->limit(50)
            ->get();
    }

    public function setMediaForm(): void
    {
        if (count($this->selected) === 1) {
            $item = App::make(Media::class)->find(Arr::first($this->selected)['id']);
            if ($item) {
                $this->form->fill($item->toArray());
            }
        } else {
            $this->form->fill();
        }
    }

    public function addFilesAction(): Action
    {
        return Action::make('add_files')
            ->button()
            ->size('sm')
            ->color('primary')
            ->label(__('curator::views.panel.add_files'))
            ->action(function (): void {
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
            });
    }

    public function cancelEditAction(): Action
    {
        return Action::make('add_files')
            ->button()
            ->size('sm')
            ->color('gray')
            ->label(__('curator::views.panel.edit_cancel'))
            ->action(function (): void {
                $this->editMediaForm->fill();
                $this->selected = [];
            });
    }

    public function destroyAction(): Action
    {
        return Action::make('destroy')
            ->label(__('curator::views.panel.edit_delete'))
            ->color('danger')
            ->icon('heroicon-s-trash')
            ->iconButton()
            ->extraAttributes([
                'style' => 'border: none;',
            ])
            ->action(function (array $arguments): void {
                if (empty($arguments)) {
                    return;
                }

                try {
                    $item = App::make(Media::class)->find($arguments['item']['id']);
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
            });
    }

    public function downloadAction(): Action
    {
        return Action::make('download')
            ->label(__('curator::views.panel.download'))
            ->icon('heroicon-s-arrow-down-tray')
            ->color('gray')
            ->iconButton()
            ->extraAttributes([
                'style' => 'border: none;',
            ])
            ->action(function (array $arguments): ?StreamedResponse {
                if (empty($arguments)) {
                    return null;
                }
                return Storage::disk($arguments['item']['disk'])->download($arguments['item']['path']);
            });
    }

    public function insertMediaAction(): Action
    {
        return Action::make('insert_media')
            ->button()
            ->size('sm')
            ->color('success')
            ->label(__('curator::views.panel.use_selected_image'))
            ->action(function (): void {
                $this->dispatch('insert-media', [
                    'statePath' => $this->statePath,
                    'media' =>  $this->selected,
                ]);
            });
    }

    public function updateFileAction(): Action
    {
        return Action::make('update_file')
            ->button()
            ->size('sm')
            ->color('primary')
            ->label(__('curator::views.panel.edit_save'))
            ->action(function (): void {
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
            });
    }

    public function viewAction(): Action
    {
        return Action::make('view')
            ->label(__('curator::views.panel.view'))
            ->icon('heroicon-s-eye')
            ->color('gray')
            ->iconButton()
            ->extraAttributes([
                'style' => 'border: none;',
            ])
            ->url(function (array $arguments): ?string {
                return $arguments['item']['url'] ?? null;
            }, true);
    }

    public function render(): View
    {
        return view('curator::components.modals.curator-panel');
    }
}
