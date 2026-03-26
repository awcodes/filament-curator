<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Components\Modals\Concerns\HasBreadcrumbs;
use Awcodes\Curator\Components\Modals\Concerns\InteractsWithStorage;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Awcodes\Curator\Resources\Media\Schemas\MediaForm;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorPanel extends Component implements HasActions, HasSchemas
{
    use HasBreadcrumbs;
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithStorage;
    use WithPagination;

    public ?array $settings = [];

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

    public bool|Closure $isTenantAware = true;

    public ?string $tenantOwnershipRelationshipName = null;

    public bool $isMultiple = false;

    public ?int $maxItems = null;

    public ?int $maxSize = null;

    public ?int $maxWidth = null;

    public ?int $minSize = null;

    public ?int $mediaId = null;

    public PathGenerator|string|null $pathGenerator = null;

    public string $search = '';

    public array $selected = [];

    public int $defaultLimit = 25;

    public ?string $modalId = null;

    public ?string $statePath = null;

    public ?string $context = null;

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

    public ?array $rules = null;

    public function mount(): void
    {
        foreach ($this->settings as $key => $value) {
            $this->{$key} = $value;
        }

        $this->getDirectories();

        $this->breadcrumbs[] = $this->directory;

        $this->files = $this->getFiles();

        if (filled($this->selected)) {
            $this->selected = array_values($this->selected);
        }

        $this->form->fill();
    }

    /** @throws Exception */
    public function form(Schema $schema): Schema
    {
        if ($this->maxItems !== null && $this->maxItems !== 0) {
            $this->validationRules = array_filter($this->validationRules, fn ($value): bool => $value !== 'array' && ! str_starts_with((string) $value, 'max:'));
        }

        return $schema
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

    /**
     * @throws BindingResolutionException
     */
    public function getFiles(int $page = 0, bool $excludeSelected = false): array
    {
        $files = App::make(Media::class)::query()
            ->where(function ($query): void {
                $query->where('directory', $this->directory);
                if (filled($this->directory)) {
                    $query->orWhere('directory', 'LIKE', $this->directory.'/%');
                }
            })
            ->when(filament()->hasTenancy() && $this->isTenantAware, fn ($query) => $query->where($this->tenantOwnershipRelationshipName.'_id', filament()->getTenant()->id))
//            ->when($this->selected, function ($query, $selected) {
//                $selected = collect($selected)->pluck('id')->toArray();
//
//                return $query->whereNotIn('id', $selected);
//            })
            ->when(filled($this->acceptedFileTypes) && ! $this->showAll, function ($query) {
                $types = $this->acceptedFileTypes;
                $query = $query->whereIn('type', $types);
                $wildcardTypes = collect($types)->filter(fn ($type): bool => str_contains($type, '*'));
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

        return collect($items)->map(fn ($item) => $item->toArray())->toArray();
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

    public function removeFromFiles(int|string $id): void
    {
        $this->files = collect($this->files)->reject(fn (array $selectedItem): bool => $selectedItem['id'] === $id)->toArray();
    }

    public function updatedSearch(): void
    {
        if ($this->search === '' || $this->search === '0') {
            $this->files = $this->getFiles();
        } else {
            $this->files = App::make(Media::class)
                ->when($this->isLimitedToDirectory, fn ($query) => $query->where('directory', $this->directory))
                ->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('title', 'like', '%'.$this->search.'%')
                ->orWhere('alt', 'like', '%'.$this->search.'%')
                ->orWhere('caption', 'like', '%'.$this->search.'%')
                ->orWhere('description', 'like', '%'.$this->search.'%')
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
            ->visible(fn (): bool => count($this->form->getRawState()['files_to_add'] ?? []) !== 0)
            ->disabled(fn (): bool => count($this->form->getRawState()['files_to_add'] ?? []) === 0)
            ->action(function () use ($insertAfter): void {
                $media = self::createMediaFiles();

                $this->form->fill();

                $this->files = [
                    ...$media,
                    ...$this->files,
                ];

                foreach ($media as $item) {
                    $this->selected[] = $item;
                }

                if ($insertAfter) {
                    $this->dispatch('insert-media', ['statePath' => $this->statePath, 'media' => $this->selected, 'context' => $this->context]);
                }
            });
    }

    public function addInsertFilesAction(): Action
    {
        return $this->addFilesAction(true)
            ->name('addInsertFiles')
            ->color('success')
            ->label(trans('curator::views.panel.use_selected_image'))
            ->visible(fn (): bool => count($this->form->getRawState()['files_to_add'] ?? []) !== 0);
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function editItemAction(): Action
    {
        return Action::make('editItem')
            ->label(trans('curator::views.panel.edit'))
            ->color('gray')
            ->icon(Heroicon::Pencil)
            ->modalWidth(Width::Medium)
            ->schema(App::make(MediaForm::class)::getAdditionalInformationFormSchema())
            ->fillForm(function (array $arguments): array {
                $record = App::make(Media::class)::query()->where('id', $arguments['item']['id'])->first() ?? null;

                return $record ? $record->toArray() : [];
            })
            ->action(function (array $data, array $arguments): void {
                try {
                    $record = App::make(Media::class)->find($arguments['item']['id']);

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
            ->icon(Heroicon::Trash)
            ->requiresConfirmation()
            ->action(function (array $arguments): void {
                if ($arguments === []) {
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
                if ($arguments === []) {
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
                $this->dispatch('insert-media', ['statePath' => $this->statePath, 'media' => $this->selected, 'context' => $this->context]);
            });
    }

    public function viewItemAction(): Action
    {
        return Action::make('viewItem')
            ->label(trans('curator::views.panel.view'))
            ->icon('heroicon-s-eye')
            ->color('gray')
            ->url(function (array $arguments): ?string {
                if ($arguments === []) {
                    return null;
                }

                return $arguments['item']['url'] ?? null;
            }, true);
    }

    public function render(): View
    {
        return view('curator::livewire.curator-panel');
    }

    protected function createMediaFiles(): array
    {
        $media = [];
        $formData = $this->form->getState();

        foreach ($formData['files_to_add'] as $item) {
            $item['exif'] = empty($item['exif']) ? null : Curator::sanitizeExif($item['exif']);
            $item['title'] = pathinfo((string) ($formData['originalFilenames'][$item['path']] ?? null), PATHINFO_FILENAME);

            $media[] = tap(
                App::make(Media::class)->create($item),
                fn (Media $media): string => $media->getPrettyName(),
            )->toArray();
        }

        return $media;
    }
}
