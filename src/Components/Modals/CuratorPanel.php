<?php

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\CuratorPlugin;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Awcodes\Curator\Resources\MediaResource;
use Awcodes\Curator\Support\MediaScope;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\View as FormView;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use JsonException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorPanel extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use WithPagination;

    #[Locked]
    public array $acceptedFileTypes = [];

    #[Locked]
    public string $context = 'create';

    public ?array $data = [];

    #[Locked]
    public string $directory = 'media';

    #[Locked]
    public string $diskName = 'public';

    #[Locked]
    public ?array $files = [];

    #[Locked]
    public ?string $imageCropAspectRatio = null;

    #[Locked]
    public ?string $imageResizeMode = null;

    #[Locked]
    public ?string $imageResizeTargetWidth = null;

    #[Locked]
    public ?string $imageResizeTargetHeight = null;

    #[Locked]
    public bool $isLimitedToDirectory = false;

    #[Locked]
    public bool | Closure $isTenantAware = true;

    #[Locked]
    public ?string $tenantOwnershipRelationshipName = null;

    #[Locked]
    public bool $isMultiple = false;

    #[Locked]
    public ?int $maxItems = null;

    #[Locked]
    public ?int $maxSize = null;

    #[Locked]
    public ?int $maxWidth = null;

    #[Locked]
    public ?int $minSize = null;

    #[Locked]
    public ?int $mediaId = null;

    #[Locked]
    public PathGenerator | string | null $pathGenerator = null;

    public string $search = '';

    public array $selected = [];

    /**
     * The ids the picker held when it opened the panel, from its encrypted
     * settings. They may be outside the panel's scope, such as media saved on
     * the record before the field's settings changed, and stay selectable.
     */
    #[Locked]
    public array $heldIds = [];

    #[Locked]
    public int $defaultLimit = 25;

    #[Locked]
    public ?string $modalId = null;

    #[Locked]
    public ?string $statePath;

    #[Locked]
    public bool $shouldPreserveFilenames = false;

    #[Locked]
    public array $types = [];

    #[Locked]
    public array $validationRules = [];

    #[Locked]
    public string $visibility = 'public';

    #[Locked]
    public array $originalFilenames = [];

    #[Locked]
    public int $currentPage = 0;

    #[Locked]
    public int $mediaCount = 0;

    #[Locked]
    public int $lastPage = 0;

    #[Locked]
    public string $defaultSort = 'desc';

    #[Locked]
    public ?Media $mediaClass = null;

    public function mount(): void
    {
        $this->form->fill();
        $this->mediaClass = App::make(Media::class);
    }

    /**
     * How long an encrypted settings payload can be used to open the panel.
     */
    protected const SETTINGS_TTL_SECONDS = 600;

    protected const SETTINGS_PURPOSE = 'curator-panel-settings';

    /**
     * Encrypt the settings an opener sends with the `open-modal` event.
     *
     * The event travels through the browser, which could otherwise change the
     * disk, directory, accepted types or limits the panel uploads with, so the
     * panel only accepts settings encrypted with the application key.
     */
    public static function encryptSettings(array $settings): string
    {
        return Crypt::encryptString(json_encode([
            'purpose' => self::SETTINGS_PURPOSE,
            'expires' => now()->addSeconds(self::SETTINGS_TTL_SECONDS)->getTimestamp(),
            'settings' => $settings,
        ], JSON_THROW_ON_ERROR));
    }

    #[On('open-modal')]
    public function openModal(string $id, mixed $settings = null): void
    {
        if ($id !== 'curator-panel') {
            return;
        }

        $settings = $this->decryptSettings($settings);

        if ($settings === null) {
            return;
        }

        $this->acceptedFileTypes = $settings['acceptedFileTypes'] ?? [];
        $this->defaultSort = ($settings['defaultSort'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $this->directory = $settings['directory'] ?? 'media';
        $this->diskName = $settings['diskName'] ?? 'public';
        $this->imageCropAspectRatio = $settings['imageCropAspectRatio'] ?? null;
        $this->imageResizeMode = $settings['imageResizeMode'] ?? null;
        $this->imageResizeTargetWidth = $settings['imageResizeTargetWidth'] ?? null;
        $this->imageResizeTargetHeight = $settings['imageResizeTargetHeight'] ?? null;
        $this->isLimitedToDirectory = (bool) ($settings['isLimitedToDirectory'] ?? false);
        $this->isMultiple = (bool) ($settings['isMultiple'] ?? false);
        $this->isTenantAware = (bool) ($settings['isTenantAware'] ?? true);
        $this->tenantOwnershipRelationshipName = $settings['tenantOwnershipRelationshipName'] ?? null;
        $this->maxItems = $settings['maxItems'] ?? null;
        $this->maxSize = $settings['maxSize'] ?? null;
        $this->maxWidth = $settings['maxWidth'] ?? 0;
        $this->minSize = $settings['minSize'] ?? null;
        $this->pathGenerator = $settings['pathGenerator'] ?? null;
        $this->validationRules = array_values(array_filter((array) ($settings['rules'] ?? []), 'is_string'));
        $this->shouldPreserveFilenames = (bool) ($settings['shouldPreserveFilenames'] ?? false);
        $this->statePath = $settings['statePath'] ?? null;
        $this->types = $settings['types'] ?? [];
        $this->visibility = $settings['visibility'] ?? 'public';

        $this->heldIds = MediaScope::extractIds($settings['heldIds'] ?? []);

        // The opener's selection comes from the field's state, which the browser
        // can change, so only its ids are used and the records are loaded again.
        $this->selected = $this->resolveSelection((array) ($settings['selected'] ?? []));

        $this->files = $this->getFiles();

        $this->form->fill();
    }

    /**
     * Returns null, so the panel keeps its current settings, for anything that
     * is not an unexpired payload from encryptSettings().
     */
    protected function decryptSettings(mixed $payload): ?array
    {
        if (! is_string($payload) || $payload === '') {
            return null;
        }

        try {
            $decoded = json_decode(Crypt::decryptString($payload), true, 512, JSON_THROW_ON_ERROR);
        } catch (DecryptException | JsonException) {
            return null;
        }

        if (
            ! is_array($decoded)
            || ($decoded['purpose'] ?? null) !== self::SETTINGS_PURPOSE
            || ! is_int($decoded['expires'] ?? null)
            || $decoded['expires'] < now()->getTimestamp()
            || ! is_array($decoded['settings'] ?? null)
        ) {
            return null;
        }

        return $decoded['settings'];
    }

    /**
     * Load media by the ids in a selection, in the selection's order, within the
     * panel's scope, or held by the picker when it opened the panel. Anything
     * else the selection carries is ignored.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function resolveSelection(array $selection): array
    {
        return $this->getMediaScope()
            ->resolve($selection, $this->heldIds)
            ->map(fn (Media $media): array => $media->toArray())
            ->values()
            ->all();
    }

    /**
     * The media this panel may list and select: its disk, the types it
     * accepts, its directory when it's limited to one, and the current tenant.
     */
    public function getMediaScope(): MediaScope
    {
        return new MediaScope(
            disk: $this->diskName,
            acceptedFileTypes: $this->types,
            directory: $this->directory,
            isLimitedToDirectory: $this->isLimitedToDirectory,
            isTenantAware: (bool) $this->isTenantAware,
            tenantOwnershipRelationshipName: $this->tenantOwnershipRelationshipName,
        );
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
            ->schema([
                Uploader::make('files_to_add')
                    ->visible(function () {
                        return count($this->selected) !== 1 &&
                            (
                                is_null(Gate::getPolicyFor($this->mediaClass)) ||
                                Gate::allows('create', $this->mediaClass)
                            );
                    })
                    ->hiddenLabel()
                    ->required()
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
                Group::make([
                    FormView::make('preview')
                        ->view('curator::components.forms.edit-preview', [
                            'file' => Arr::first($this->selected),
                            'actions' => [
                                $this->viewAction(),
                                $this->downloadAction(),
                                $this->destroyAction(),
                            ],
                        ]),
                    ...collect(App::make(MediaResource::class)->getAdditionalInformationFormSchema())
                        ->map(function ($field) {
                            return $field->disabled(function () {
                                return ! CuratorPlugin::get()->authorize('update');
                            });
                        })->toArray(),
                ])->visible(fn () => filled($this->selected) && count($this->selected) === 1),
            ])->statePath('data');
    }

    public function getFiles(int $page = 0, bool $excludeSelected = false): array
    {
        $files = $this->getMediaScope()->query()
            ->when($this->selected, function ($query, $selected) {
                return $query->whereKeyNot(MediaScope::extractIds($selected));
            })
            ->orderBy('created_at', $this->defaultSort);

        $paginator = $files->paginate($this->defaultLimit, page: $page);

        $this->currentPage = $paginator->currentPage();
        $this->mediaCount = $paginator->total();
        $this->lastPage = $paginator->lastPage();

        $items = $paginator->items();

        if (! $excludeSelected && $this->selected) {
            $selectedItems = $this->getMediaScope()->resolve($this->selected, $this->heldIds);

            array_unshift($items, ...$selectedItems);

            $this->setMediaForm();
            $this->context = count($this->selected) === 1 ? 'edit' : 'create';
        }

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

    public function addToSelection(int | string $id): void
    {
        $item = collect($this->files)->firstWhere('id', $id);

        if (! $item) {
            return;
        }

        if ($this->isMultiple) {
            $this->selected[] = $item;
        } else {
            $this->selected = [$item];
        }

        $this->context = count($this->selected) === 1 ? 'edit' : 'create';
        $this->setMediaForm();
    }

    public function removeFromSelection(int | string $id): void
    {
        $this->selected = collect($this->selected)->reject(function ($selectedItem) use ($id) {
            return $selectedItem['id'] === $id;
        })->toArray();

        $this->context = count($this->selected) === 1 ? 'edit' : 'create';
    }

    public function removeFromFiles(int | string $id): void
    {
        $this->files = collect($this->files)->reject(function ($selectedItem) use ($id) {
            return $selectedItem['id'] === $id;
        })->toArray();

        $this->context = filled($this->selected) ? 'edit' : 'create';
    }

    public function updatedSearch(): void
    {
        $this->files = $this->getMediaScope()->query()
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('title', 'like', '%' . $this->search . '%')
                    ->orWhere('alt', 'like', '%' . $this->search . '%')
                    ->orWhere('caption', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->limit(50)
            ->get()
            ->toArray();
    }

    protected function setMediaForm(): void
    {
        if (count($this->selected) === 1) {
            $item = $this->getMediaScope()->resolve([Arr::first($this->selected)['id'] ?? null], $this->heldIds)->first();
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
            ->disabled(function (): bool {
                return count($this->form->getRawState()['files_to_add'] ?? []) === 0;
            })
            ->visible(function () {
                return CuratorPlugin::get()->authorize('create');
            })
            ->action(function () use ($insertAfter): void {
                $media = self::createMediaFiles($this->form->getState());

                $this->form->fill();

                $this->files = [
                    ...$media,
                    ...$this->files,
                ];

                if ($insertAfter) {
                    $this->dispatch(
                        'insert-content',
                        type: 'media',
                        statePath: $this->statePath,
                        media: $media
                    );

                    $this->dispatch('close-modal', id: $this->modalId ?? 'curator-panel');

                    return;
                }

                foreach ($media as $item) {
                    $this->addToSelection($item['id']);
                }
            });
    }

    public function addInsertFilesAction(): Action
    {
        return $this->addFilesAction(true)
            ->name('addInsertFiles')
            ->color('success')
            ->label(__('curator::views.panel.use_selected_image'));
    }

    public function cancelEditAction(): Action
    {
        return Action::make('cancelEdit')
            ->button()
            ->size('sm')
            ->color('gray')
            ->label(trans('curator::views.panel.edit_cancel'))
            ->action(function (): void {
                $this->form->fill();
                $this->selected = [];
                $this->context = 'create';
            });
    }

    /**
     * Resolve the Media record targeted by a per-item action, enforcing both the
     * panel's scope and the model's authorization policy for the given ability.
     *
     * The record id arrives from client-supplied state (Livewire action arguments
     * or the `selected` property), so it must never be trusted directly. This
     * uses the same scope as the panel's list and search queries, and the
     * null-policy fallback keeps the existing "allow when no policy is registered"
     * behaviour while honouring a policy when one exists.
     *
     * Returns null when the id is missing, the record is out of the panel's scope, or the
     * ability is denied; callers treat null as "do nothing".
     */
    protected function resolveAuthorizedMedia(string | int | null $id, string $ability): ?Media
    {
        if (blank($id)) {
            return null;
        }

        $record = $this->getMediaScope()->query()
            ->whereKey($id)
            ->first();

        if (! $record) {
            return null;
        }

        return (is_null(Gate::getPolicyFor($record)) || Gate::allows($ability, $record)) ? $record : null;
    }

    public function destroyAction(): Action
    {
        return Action::make('destroy')
            ->label(trans('curator::views.panel.edit_delete'))
            ->color('danger')
            ->icon('heroicon-s-trash')
            ->iconButton()
            ->extraAttributes([
                'style' => 'border: none; margin: 0;',
            ])
            ->requiresConfirmation()
            ->visible(function () {
                return CuratorPlugin::get()->authorize('delete');
            })
            ->action(function (array $arguments): void {
                if (empty($arguments)) {
                    return;
                }

                try {
                    $item = $this->resolveAuthorizedMedia($arguments['item']['id'] ?? null, 'delete');
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
                        throw new Exception;
                    }
                } catch (Exception) {
                    Notification::make('curator_delete_error')
                        ->danger()
                        ->body(trans('curator::notifications.delete_error'))
                        ->send();
                }
            });
    }

    public function downloadAction(): Action
    {
        return Action::make('download')
            ->label(trans('curator::views.panel.download'))
            ->icon('heroicon-s-arrow-down-tray')
            ->color('gray')
            ->iconButton()
            ->extraAttributes([
                'style' => 'border: none; margin: 0;',
            ])
            ->visible(function () {
                return CuratorPlugin::get()->authorize('download');
            })
            ->action(function (array $arguments): ?StreamedResponse {
                if (empty($arguments)) {
                    return null;
                }

                $record = $this->resolveAuthorizedMedia($arguments['item']['id'] ?? null, 'view');

                if (! $record) {
                    return null;
                }

                // Resolve the disk and path from the authorized record rather than the
                // client-supplied arguments, so a tampered payload cannot stream an
                // arbitrary file from an arbitrary disk.
                return Storage::disk($record->disk)->download($record->path);
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
                // The selection can be changed from the browser, so the media
                // sent to the field is loaded again by id within the tenant scope.
                $media = $this->resolveSelection($this->selected);

                if (! $this->isMultiple) {
                    $media = array_slice($media, 0, 1);
                }

                $this->selected = $media;

                $this->dispatch(
                    'insert-content',
                    type: 'media',
                    statePath: $this->statePath,
                    media: $media
                );

                $this->dispatch('close-modal', id: $this->modalId ?? 'curator-panel');
            });
    }

    public function updateFileAction(): Action
    {
        return Action::make('updateFile')
            ->button()
            ->size('sm')
            ->color('primary')
            ->label(trans('curator::views.panel.edit_save'))
            ->visible(function () {
                return CuratorPlugin::get()->authorize('update');
            })
            ->action(function (): void {
                try {
                    $item = $this->resolveAuthorizedMedia(Arr::first($this->selected)['id'] ?? null, 'update');
                    if ($item) {
                        $item->update($this->form->getState());

                        $this->selected = collect($this->selected)->map(function ($selectedItem) use ($item) {
                            if ($selectedItem['id'] === $item->id) {
                                return $item->refresh();
                            }

                            return $selectedItem;
                        })->toArray();

                        Notification::make('curator_update_success')
                            ->success()
                            ->body(trans('curator::notifications.update_success'))
                            ->send();
                    } else {
                        throw new Exception;
                    }
                } catch (Exception) {
                    Notification::make('curator_update_error')
                        ->danger()
                        ->body(trans('curator::notifications.update_error'))
                        ->send();
                }
            });
    }

    public function viewAction(): Action
    {
        return Action::make('view')
            ->label(trans('curator::views.panel.view'))
            ->icon('heroicon-s-eye')
            ->color('gray')
            ->iconButton()
            ->extraAttributes([
                'style' => 'border: none; margin: 0;',
            ])
            ->url(function (array $arguments): ?string {
                if (empty($arguments)) {
                    return null;
                }

                return $arguments['item']['url'] ?? null;
            }, true);
    }

    public function render(): View
    {
        return view('curator::components.modals.curator-panel');
    }

    protected function createMediaFiles(array $formData): array
    {
        $media = [];

        foreach ($formData['files_to_add'] as $item) {
            $item['title'] = pathinfo($formData['originalFilenames'][$item['path']] ?? null, PATHINFO_FILENAME);

            $media[] = tap(
                $this->mediaClass->create($item),
                fn (Media $media) => $media->getPrettyName(),
            )->toArray();
        }

        return $media;
    }
}
