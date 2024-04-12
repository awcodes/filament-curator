<?php

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Components\Modals\Concerns\HasBreadcrumbs;
use Awcodes\Curator\Components\Modals\Concerns\InteractsWithStorage;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Awcodes\Curator\Resources\MediaResource;
use Awcodes\Pounce\Enums\MaxWidth;
use Awcodes\Pounce\PounceComponent;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth as FilamentMaxWidth;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorPanel extends PounceComponent implements HasActions, HasForms
{
    use HasBreadcrumbs;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithStorage;
    use WithPagination;

    public array $acceptedFileTypes = [];

    public ?array $panelData = [];

    public ?string $directory = null;

    public string $diskName = 'public';

    public ?array $files = [];

    public ?string $imageCropAspectRatio = null;

    public ?string $imageResizeMode = null;

    public ?string $imageResizeTargetWidth = null;

    public ?string $imageResizeTargetHeight = null;

    public bool $isLimitedToDirectory = false;

    public bool | Closure $isTenantAware = true;

    public ?string $tenantOwnershipRelationshipName = null;

    public bool $isMultiple = false;

    public ?int $maxItems = null;

    public ?int $maxSize = null;

    public ?int $maxWidth = null;

    public ?int $minSize = null;

    public ?int $mediaId = null;

    public PathGenerator | string | null $pathGenerator = null;

    public string $search = '';

    public array $selected = [];

    public int $defaultLimit = 25;

    public ?string $modalId = null;

    public ?string $statePath;

    public bool $shouldPreserveFilenames = false;

    public array $types = [];

    public array $validationRules = [];

    public string $visibility = 'public';

    public array $originalFilenames = [];

    public int $currentPage = 0;

    public int $mediaCount = 0;

    public int $lastPage = 0;

    public string $defaultSort = 'desc';

    public bool $shouldPrefetchFiles = false;

    public bool $showAll = false;

    public function mount(): void
    {
        $this->getDirectories();

        $this->breadcrumbs[] = $this->directory;

        $this->files = $this->getFiles();

        if (filled($this->selected)) {
            $this->selected = array_values($this->selected);
        }

        $this->form->fill();
    }

    public static function getMaxWidth(): MaxWidth
    {
        return MaxWidth::Screen;
    }

    public function form(Form $form): Form
    {
        if ($this->maxItems) {
            $this->validationRules = array_filter($this->validationRules, function ($value) {
                if ($value === 'array' || str_starts_with($value, 'max:')) {
                    return false;
                }

                return true;
            });
        }

        return $form
            ->statePath('panelData')
            ->schema([
                Uploader::make('files_to_add')
                    ->hiddenLabel()
                    ->multiple()
                    ->label(trans('curator::forms.fields.file'))
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
                    ->imageResizeTargetHeight($this->imageResizeTargetHeight)
                    ->storeFileNamesIn('originalFilenames'),
            ]);
    }

    public function getFiles(int $page = 0, bool $excludeSelected = false): array
    {
        $files = Media::query()
            ->where('directory', $this->directory)
            ->when(filament()->hasTenancy() && $this->isTenantAware, function ($query) {
                return $query->where($this->tenantOwnershipRelationshipName . '_id', filament()->getTenant()->id);
            })
//            ->when($this->selected, function ($query, $selected) {
//                $selected = collect($selected)->pluck('id')->toArray();
//
//                return $query->whereNotIn('id', $selected);
//            })
            ->when(filled($this->acceptedFileTypes) && ! $this->showAll, function ($query) {
                $types = $this->acceptedFileTypes;
                $query = $query->whereIn('mime', $types);
                $wildcardTypes = collect($types)->filter(fn ($type) => str_contains($type, '*'));
                $wildcardTypes?->map(fn ($type) => $query->orWhere('type', 'LIKE', str_replace('*', '%', $type)));

                return $query;
            })
            ->orderBy('created_at', $this->defaultSort);

        $paginator = $files->paginate($this->defaultLimit, page: $page);

        $this->currentPage = $paginator->currentPage();
        $this->mediaCount = $paginator->total();
        $this->lastPage = $paginator->lastPage();

        $items = $paginator->items();

//        if (! $excludeSelected && $this->selected) {
//            $selected = collect($this->selected)->pluck('id')->toArray();
//
//            $selectedItems = Media::query()
//                ->whereIn('id', $selected)
//                ->get()
//                ->sortBy(function ($model) use ($selected) {
//                    return array_search($model->id, $selected);
//                });
//
//            array_unshift($items, ...$selectedItems);
//
//            $this->setMediaForm();
//        }

        $this->getSubDirectories();
        $this->getBreadCrumbs();

        return collect($items)->map(function ($item) {
            return $item->toArray();
        })->toArray();
    }

    public function loadMoreFiles(): void
    {
        if ($this->currentPage === $this->lastPage) {
            return;
        }

        $this->files = [
            ...$this->files,
            ...$this->getFiles($this->currentPage + 1, true),
        ];
    }

    public function removeFromFiles(int | string $id): void
    {
        $this->files = collect($this->files)->reject(function ($selectedItem) use ($id) {
            return $selectedItem['id'] === $id;
        })->toArray();
    }

    public function updatedSearch(): void
    {
        if (empty($this->search)) {
            $this->files = $this->getFiles();
        } else {
            $this->files = App::make(Media::class)
                ->when($this->isLimitedToDirectory, function ($query) {
                    return $query->where('directory', $this->directory);
                })
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('title', 'like', '%' . $this->search . '%')
                ->orWhere('alt', 'like', '%' . $this->search . '%')
                ->orWhere('caption', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->limit(50)
                ->get()
                ->toArray();
        }
    }

    public function setMediaForm(): void
    {
        if (count($this->selected) === 1) {
            $item = App::make(Media::class)->find(Arr::first($this->selected));
            if ($item) {
                $this->form->fill($item->toArray());
            }
        } else {
            $this->form->fill();
        }
    }

    public function addFilesAction(bool $insertAfter = false): Action
    {
        return Action::make('addFiles')
            ->button()
            ->size('sm')
            ->color('primary')
            ->label(trans('curator::views.panel.add_files'))
            ->visible(function (): bool {
                return count($this->form->getRawState()['files_to_add'] ?? []) !== 0;
            })
            ->disabled(function (): bool {
                return count($this->form->getRawState()['files_to_add'] ?? []) === 0;
            })
            ->action(function () use ($insertAfter): void {
                $media = self::createMediaFiles();

                $this->form->fill();

                if ($insertAfter) {
                    $this->dispatch(
                        'insert-content',
                        type: 'media',
                        statePath: $this->statePath,
                        media: $media
                    );

                    $this->dispatch('unPounce');

                    return;
                }

                $this->files = [
                    ...$media,
                    ...$this->files,
                ];

                foreach ($media as $item) {
                    $this->selected[] = (string) $item['id'];
                }
            });
    }

    public function addInsertFilesAction(): Action
    {
        return $this->addFilesAction(true)
            ->name('addInsertFiles')
            ->color('success')
            ->label(trans('curator::views.panel.use_selected_image'))
            ->visible(function (): bool {
                return count($this->form->getRawState()['files_to_add'] ?? []) !== 0;
            });
    }

    public function editItemAction(): Action
    {
        return Action::make('editItem')
            ->label(trans('curator::views.panel.edit'))
            ->color('gray')
            ->icon('heroicon-s-pencil')
            ->modalWidth(FilamentMaxWidth::Medium)
            ->record(fn (array $arguments) => Media::query()->where('id', $arguments['item']['id'])->first() ?? null)
            ->fillForm(fn (Media $record) => $record->toArray())
            ->form(App::make(MediaResource::class)->getAdditionalInformationFormSchema())
            ->action(function (array $data, Media $record): void {
                try {
                    $record->update($data);

                    Notification::make('curator_update_success')
                        ->success()
                        ->body(trans('curator::notifications.update_success'))
                        ->send();
                } catch (Exception) {
                    Notification::make('curator_update_error')
                        ->danger()
                        ->body(trans('curator::notifications.update_error'))
                        ->send();
                }
            });
    }

    public function destroyItemAction(): Action
    {
        return Action::make('destroyItem')
            ->label(trans('curator::views.panel.edit_delete'))
            ->color('danger')
            ->icon('heroicon-s-trash')
            ->requiresConfirmation()
            ->action(function (array $arguments): void {
                if (empty($arguments)) {
                    return;
                }

                try {
                    $item = App::make(Media::class)->find($arguments['item']['id']);
                    if ($item) {
                        $this->form->fill();
                        $item->delete();
                        $this->selected = [];
                        $this->removeFromFiles($arguments['item']['id']);

                        Notification::make('curator_delete_success')
                            ->success()
                            ->body(trans('curator::notifications.delete_success'))
                            ->send();
                    } else {
                        throw new Exception();
                    }
                } catch (Exception) {
                    Notification::make('curator_delete_error')
                        ->danger()
                        ->body(trans('curator::notifications.delete_error'))
                        ->send();
                }
            });
    }

    public function downloadItemAction(): Action
    {
        return Action::make('downloadItem')
            ->label(trans('curator::views.panel.download'))
            ->icon('heroicon-s-arrow-down-tray')
            ->color('gray')
            ->action(function (array $arguments): ?StreamedResponse {
                if (empty($arguments)) {
                    return null;
                }

                return Storage::disk($arguments['item']['disk'])->download($arguments['item']['path']);
            });
    }

    public function insertMediaAction(): Action
    {
        return Action::make('insertMedia')
            ->button()
            ->size('sm')
            ->color('success')
            ->label(trans('curator::views.panel.use_selected_image'))
            ->action(function (): void {
                $this->dispatch(
                    'insert-content',
                    type: 'media',
                    statePath: $this->statePath,
                    media: $this->selected
                );

                $this->dispatch('unPounce');
            });
    }

    public function viewItemAction(): Action
    {
        return Action::make('viewItem')
            ->label(trans('curator::views.panel.view'))
            ->icon('heroicon-s-eye')
            ->color('gray')
            ->url(function (array $arguments): ?string {
                if (empty($arguments)) {
                    return null;
                }

                return $arguments['item']['url'] ?? null;
            }, true);
    }

    protected function createMediaFiles(): array
    {
        $media = [];
        $formData = $this->form->getState();

        foreach ($formData['files_to_add'] as $item) {
            $item['exif'] = ! empty($item['exif']) ? Curator::sanitizeExif($item['exif']) : null;
            $item['title'] = pathinfo($formData['originalFilenames'][$item['path']] ?? null, PATHINFO_FILENAME);

            $media[] = tap(
                App::make(Media::class)->create($item),
                fn (Media $media) => $media->getPrettyName(),
            )->toArray();
        }

        return $media;
    }

    public function render(): View
    {
        return view('curator::components.modals.curator-panel');
    }
}
