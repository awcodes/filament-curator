<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals;

use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Components\Modals\Concerns\HasBreadcrumbs;
use Awcodes\Curator\Components\Modals\Concerns\InteractsWithStorage;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;
use Awcodes\Curator\Resources\Media\MediaResource;
use Awcodes\Curator\Resources\Media\Schemas\MediaForm;
use Awcodes\Curator\Support\MediaScope;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorPanel extends Component implements HasActions, HasSchemas
{
    use HasBreadcrumbs;
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithStorage;
    use WithPagination;

    /**
     * @var array<int, string>
     */
    protected const SEARCH_COLUMNS = ['name', 'title', 'alt', 'caption', 'description'];

    /*
     * The panel is configured once, from the settings the picker passes when it renders it. Everything that decides
     * what may be uploaded, where it is stored and which records are listed is locked, so the browser can't change
     * it; only the search, the upload form and the selection accept client input.
     */
    #[Locked]
    public ?array $settings = [];

    #[Locked]
    public array $acceptedFileTypes = [];

    public ?array $panelData = [];

    #[Locked]
    public ?string $directory = null;

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
    public ?int $minSize = null;

    #[Locked]
    public int | string | null $mediaId = null;

    #[Locked]
    public PathGenerator | string | null $pathGenerator = null;

    public string $search = '';

    public array $selected = [];

    #[Locked]
    public int $defaultLimit = 25;

    #[Locked]
    public ?string $modalId = null;

    #[Locked]
    public ?string $statePath = null;

    #[Locked]
    public ?string $context = null;

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
    public bool $shouldPrefetchFiles = false;

    #[Locked]
    public bool $showAll = false;

    #[Locked]
    public ?array $rules = null;

    public function mount(): void
    {
        foreach ($this->settings as $key => $value) {
            $this->{$key} = $value;
        }

        $this->validationRules = $this->getUploadValidationRules();

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
        return $schema
            ->statePath('panelData')
            ->schema([
                Uploader::make('files_to_add')
                    ->hiddenLabel()
                    ->multiple()
                    ->when(! $this->isMultiple, fn (Uploader $uploader): Uploader => $uploader->maxFiles(1))
                    ->label(trans('curator::forms.fields.file'))
                    ->preserveFilenames($this->shouldPreserveFilenames)
                    ->when(filled($this->minSize), fn (Uploader $uploader): Uploader => $uploader->minSize($this->minSize))
                    ->when(filled($this->maxSize), fn (Uploader $uploader): Uploader => $uploader->maxSize($this->maxSize))
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
        $files = $this->getMediaScope()->query()
            ->where(fn (Builder $query): Builder => filled($this->directory)
                ? MediaScope::whereWithinDirectory($query, $this->directory)
                : $query->whereNull('directory'))
            ->orderBy('created_at', $this->defaultSort);

        $paginator = $files->paginate($this->defaultLimit, page: $page);

        $this->currentPage = $paginator->currentPage();
        $this->mediaCount = $paginator->total();
        $this->lastPage = $paginator->lastPage();

        $items = $paginator->items();

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

    public function removeFromFiles(int | string $id): void
    {
        $this->files = collect($this->files)->reject(fn (array $selectedItem): bool => $selectedItem['id'] === $id)->toArray();
    }

    public function updatedSearch(): void
    {
        $terms = $this->getSearchTerms();

        if ($terms === []) {
            $this->files = $this->getFiles();

            return;
        }

        $this->files = $this->getMediaScope()->query()
            ->where(function ($query) use ($terms): void {
                // Every term has to match, but any of the columns may be the
                // one matching it, so "my image" finds my-image.png no matter
                // which order the words are typed in.
                foreach ($terms as $term) {
                    $query->where(function ($query) use ($term): void {
                        foreach (static::SEARCH_COLUMNS as $column) {
                            $query->orWhereRaw(
                                $query->getGrammar()->wrap($column) . " like ? escape '~'",
                                ['%' . $term . '%'],
                            );
                        }
                    });
                }
            })
            ->orderBy('created_at', $this->defaultSort)
            ->limit(50)
            ->get()
            ->toArray();

        // Search results arrive in one set, so there is nothing more to load until the search is cleared, which
        // fetches the first page again.
        $this->currentPage = $this->lastPage;
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

                // A single picker holds one item, so an upload replaces the selection rather than adding to it.
                $this->selected = $this->isMultiple
                    ? [...$this->selected, ...$media]
                    : array_slice($media, 0, 1);

                if ($insertAfter) {
                    $this->dispatchInsertMedia();
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
                $record = $this->resolveAuthorizedMedia($arguments, 'update');

                return $record instanceof Media ? $record->toArray() : [];
            })
            ->action(function (array $data, array $arguments): void {
                try {
                    $record = $this->resolveAuthorizedMedia($arguments, 'update');

                    if (! $record instanceof Media) {
                        throw new Exception();
                    }

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
        $action = Action::make('destroyItem')
            ->label(trans('curator::views.panel.edit_delete'))
            ->color('danger')
            ->icon(Heroicon::Trash)
            ->requiresConfirmation()
            // Exposes the target as $record to closures added through
            // Curator::configureDeleteActionsUsing(), e.g. a modal description.
            // Every grid item builds this action just to render its button, so
            // only resolve the item that is actually mounted, then keep it.
            ->record(function (Action $action, array $arguments): ?Media {
                $mounted = Arr::last($this->mountedActions);

                if (($mounted['name'] ?? null) !== 'destroyItem') {
                    return null;
                }

                if ((string) ($mounted['arguments']['item']['id'] ?? '') !== (string) ($arguments['item']['id'] ?? '')) {
                    return null;
                }

                $record = $this->resolveAuthorizedMedia($arguments, 'delete');

                if ($record instanceof Media) {
                    $action->record($record);
                }

                return $record;
            })
            ->action(function (array $arguments): void {
                if ($arguments === []) {
                    return;
                }

                try {
                    $item = $this->resolveAuthorizedMedia($arguments, 'delete');
                    if ($item instanceof Media) {
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

        return Curator::configureDeleteAction($action);
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

                $record = $this->resolveAuthorizedMedia($arguments, 'view');

                if (! $record instanceof Media) {
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
                $this->dispatchInsertMedia();
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

    /**
     * The media this panel may list and select, from the settings the picker passed when it rendered it. The
     * directory limit is the configured directory, not the one being browsed.
     */
    public function getMediaScope(): MediaScope
    {
        return new MediaScope(
            disk: $this->diskName,
            acceptedFileTypes: $this->showAll ? [] : $this->acceptedFileTypes,
            directory: $this->isLimitedToDirectory ? ($this->settings['directory'] ?? null) : null,
            isTenantAware: (bool) $this->isTenantAware,
            tenantOwnershipRelationshipName: $this->tenantOwnershipRelationshipName,
        );
    }

    /**
     * The selection is entangled with the browser, so only its ids are used: each item is loaded again, within the
     * panel's scope, so the picker receives the stored disk and path rather than whatever the client sent, and
     * nothing the panel wouldn't list.
     */
    protected function dispatchInsertMedia(): void
    {
        $selected = array_filter($this->selected, is_array(...));

        $media = $this->getMediaScope()->resolve($selected)
            ->when(! $this->isMultiple, fn ($items) => $items->take(1))
            ->map(fn (Media $media): array => $media->toArray())
            ->values()
            ->all();

        $this->selected = $media;

        $this->dispatch('insert-media', ['statePath' => $this->statePath, 'media' => $media, 'context' => $this->context]);
    }

    /**
     * The picker's rules validate its selection. Only the ones that describe a single file also apply to uploads;
     * anything else, such as `required`, `exists` or `min`/`max` (which count items on a picker), is left out. File
     * size limits come from minSize() and maxSize().
     *
     * @return array<string>
     */
    protected function getUploadValidationRules(): array
    {
        return collect($this->rules ?? [])
            ->filter(fn (mixed $rule): bool => is_string($rule)
                && in_array(strtolower(Str::before($rule, ':')), ['dimensions', 'mimes', 'mimetypes', 'extensions', 'image', 'file'], true))
            ->values()
            ->all();
    }

    /**
     * Split the search on the separators that show up in file names so each
     * word can be matched on its own, and escape the LIKE wildcards so a
     * literal % or _ in the search is not treated as one.
     *
     * @return array<int, string>
     */
    protected function getSearchTerms(): array
    {
        $terms = preg_split('/[\s\-_]+/', $this->search, flags: PREG_SPLIT_NO_EMPTY);

        if ($terms === false) {
            return [];
        }

        // '~' is the escape character, so an input containing one has to
        // escape it too. Backslash is avoided because MySQL and Postgres
        // disagree on how it is read inside a string literal.
        return array_map(
            fn (string $term): string => str_replace(['~', '%', '_'], ['~~', '~%', '~_'], $term),
            $terms,
        );
    }

    /**
     * Resolve the Media record targeted by a per-item action, enforcing both the
     * panel's scope and the resource's authorization policy for the given ability.
     *
     * The record id (and, previously, the disk/path) arrive as client-supplied
     * Livewire action arguments, so they must never be trusted directly. This
     * uses the same scope as the panel's list/search queries and defers to
     * MediaResource's authorization — which defaults to "allow" when the host app
     * has registered no policy, preserving existing behaviour, and honours the
     * policy when one exists.
     *
     * Returns null when the id is missing, the record is out of the panel's scope,
     * or the ability is denied; callers treat null as "do nothing".
     *
     * @param  array<string, mixed>  $arguments
     */
    protected function resolveAuthorizedMedia(array $arguments, string $ability): ?Media
    {
        $id = $arguments['item']['id'] ?? null;

        if (! is_int($id) && (! is_string($id) || $id === '')) {
            return null;
        }

        $record = $this->getMediaScope()->query()
            ->whereKey($id)
            ->first();

        if ($record === null) {
            return null;
        }

        // Resolve the resource from the container so any config override
        // (curator.resource.resource / curator.model) is respected.
        $resource = App::make(MediaResource::class);

        return $resource::can($ability, $record) ? $record : null;
    }

    protected function createMediaFiles(): array
    {
        // The upload field's state is client-writable, and the uploader passes anything that isn't a new upload
        // through as already stored file data, so only this request's uploads may reach it.
        $this->panelData['files_to_add'] = array_filter(
            Arr::wrap($this->panelData['files_to_add'] ?? []),
            fn (mixed $file): bool => $file instanceof TemporaryUploadedFile,
        );

        $media = [];
        $formData = $this->form->getState();

        foreach ($formData['files_to_add'] as $item) {
            if (! is_array($item) || ($item['disk'] ?? null) !== $this->diskName || ($item['visibility'] ?? null) !== $this->visibility) {
                continue;
            }

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
