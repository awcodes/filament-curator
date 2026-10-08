<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Concerns\CanGeneratePaths;
use Awcodes\Curator\Concerns\CanUploadFiles;
use Awcodes\Curator\CuratorPlugin;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\MediaResource;
use Awcodes\Curator\Support\MediaScope;
use Closure;
use Exception;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\HasSize;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Support\Concerns\HasColor;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CuratorPicker extends Field
{
    use CanBeOutlined;
    use CanGeneratePaths;
    use CanUploadFiles;
    use HasColor;
    use HasSize;

    protected string $view = 'curator::components.forms.picker';

    protected string | Htmlable | Closure | null $buttonLabel = null;

    protected bool | Closure | null $isConstrained = false;

    protected ?bool $isLimitedToDirectory = null;

    protected bool | Closure | null $isMultiple = false;

    protected bool | Closure | null $isTenantAware = null;

    protected ?string $tenantOwnershipRelationshipName = null;

    protected bool | Closure | null $shouldLazyLoad = null;

    protected int | Closure | null $maxItems = null;

    protected ?string $orderColumn = null;

    protected ?string $typeColumn = null;

    protected ?string $typeValue = null;

    protected string | Closure | null $relationship = null;

    protected string | Closure | null $relationshipTitleColumnName = null;

    protected bool | Closure | null $shouldDisplayAsList = null;

    protected string | Closure | null $defaultPanelSort = null;

    /** @var array<string, array<int, string>> */
    protected array $persistedMediaIds = [];

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->buttonLabel(trans('curator::views.picker.button'))
            ->size('md')
            ->color('primary')
            ->outlined();

        $this->afterStateHydrated(static function (CuratorPicker $component, mixed $state): void {
            if (blank($state)) {
                $component->state([]);

                return;
            }

            // The value is loaded again within the field's scope, so an id it wouldn't list never loads. Media already
            // saved on the record keeps loading as long as it exists and belongs to the current tenant.
            $component->state($component->toStateItems(
                $component->getMediaScope()->resolve($state, $component->getPersistedMediaIds()),
            ));
        });

        // The panel inserts its selection from the browser, so the items are loaded again by id.
        $this->afterStateUpdated(function (CuratorPicker $component, mixed $state): void {
            $media = filled($state)
                ? $component->getMediaScope()->resolve($state, $component->getPersistedMediaIds())
                : [];

            $component->state($component->toStateItems($media));
        });

        $this->dehydrateStateUsing(function (CuratorPicker $component, mixed $state): int | string | array | null {
            $ids = filled($state)
                ? $component->getMediaScope()->resolve($state, $component->getPersistedMediaIds())
                    ->map(fn (Media $media): int | string => $media->getKey())
                    ->all()
                : [];

            if ($ids === []) {
                return null;
            }

            if (count($ids) === 1 && ! $component->isMultiple()) {
                return $ids[0];
            }

            return $ids;
        });

        // The field's state is held in the browser, so a selection is checked again before it is saved.
        $this->rule(static fn (CuratorPicker $component): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($component): void {
            if (! $component->getMediaScope()->contains($value, $component->getPersistedMediaIds())) {
                $fail(trans('curator::views.picker.unavailable'));
            }
        });

        $this->registerActions([
            fn (CuratorPicker $component): Action => $component->getDownloadAction(),
            fn (CuratorPicker $component): Action => $component->getEditAction(),
            fn (CuratorPicker $component): Action => $component->getRemoveAction(),
            fn (CuratorPicker $component): Action => $component->getRemoveAllAction(),
            fn (CuratorPicker $component): Action => $component->getReorderAction(),
            fn (CuratorPicker $component): Action => $component->getViewAction(),
            fn (CuratorPicker $component): Action => $component->getPickerAction(),
        ]);
    }

    public function buttonLabel(string | Htmlable | Closure $label): static
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function constrained(bool | Closure | null $condition = true): static
    {
        $this->isConstrained = $condition;

        return $this;
    }

    public function defaultPanelSort(string | Closure | null $direction = 'desc'): static
    {
        $this->defaultPanelSort = $direction;

        return $this;
    }

    public function getDefaultPanelSort(): string
    {
        return $this->evaluate($this->defaultPanelSort) ?? 'desc';
    }

    public function getButtonLabel(): string
    {
        return $this->evaluate($this->buttonLabel);
    }

    public function getMaxItems(): ?int
    {
        return $this->evaluate($this->maxItems);
    }

    public function getOrderColumn(): string
    {
        return $this->orderColumn ?? 'order';
    }

    public function getTypeColumn(): string
    {
        return $this->typeColumn ?? 'type';
    }

    public function getTypeValue(): ?string
    {
        return $this->typeValue ?? null;
    }

    public function getRelationship(): BelongsTo | BelongsToMany | MorphMany | null
    {
        $name = $this->getRelationshipName();

        if (blank($name)) {
            return null;
        }

        return $this->getModelInstance()->{$name}();
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function getReorderAction(): Action
    {
        return Action::make('reorder')
            ->label(trans('curator::views.picker.reorder'))
            ->icon('heroicon-s-arrows-up-down')
            ->iconButton()
            ->size('xs')
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->extraAttributes(['style' => 'cursor: move;'])
            ->action(function (array $arguments, CuratorPicker $component): void {
                if (empty($arguments['items'])) {
                    return;
                }

                $state = $component->getState();

                foreach ($arguments['items'] as $key => $item) {
                    if (! str_contains($item, '-')) {
                        $uuid = (string) Str::uuid();
                        $arguments['items'][$key] = $uuid;
                        $state[$uuid] = $state[(int) $item];
                        unset($state[(int) $item]);
                    }
                }

                $items = [
                    ...array_flip($arguments['items']),
                    ...$state,
                ];

                $component->state($items);
            });
    }

    public function getDownloadAction(): Action
    {
        return Action::make('download')
            ->label(trans('curator::views.picker.download'))
            ->icon('heroicon-s-arrow-down-tray')
            ->color('gray')
            ->visible(function () {
                return CuratorPlugin::get()->authorize('download');
            })
            ->action(function (array $arguments, CuratorPicker $component): ?StreamedResponse {
                $record = $component->resolveAuthorizedMedia($arguments['uuid'] ?? null, 'view');

                if (! $record) {
                    return null;
                }

                return Storage::disk($record->disk)->download($record->path);
            });
    }

    /**
     * The field state, including each item's disk and path, comes from the
     * browser, so only the item's id is used. The record is loaded again with
     * the same tenant scoping as the media panel and checked against the Media
     * policy when one is registered.
     */
    protected function resolveAuthorizedMedia(mixed $uuid, string $ability): ?Media
    {
        if (! is_string($uuid) && ! is_int($uuid)) {
            return null;
        }

        $state = $this->getState();

        $id = is_array($state) && is_array($state[$uuid] ?? null) ? ($state[$uuid]['id'] ?? null) : null;

        if (blank($id) || ! is_scalar($id)) {
            return null;
        }

        $record = $this->getMediaScope()->resolve([$id], $this->getPersistedMediaIds())->first();

        if (! $record instanceof Media) {
            return null;
        }

        return (is_null(Gate::getPolicyFor($record)) || Gate::allows($ability, $record)) ? $record : null;
    }

    /**
     * The media this field may list, select and keep: its disk, accepted types and tenant, and its directory when
     * it's limited to one.
     */
    public function getMediaScope(): MediaScope
    {
        return new MediaScope(
            disk: $this->getDiskName(),
            acceptedFileTypes: $this->getAcceptedFileTypes(),
            directory: $this->getDirectory(),
            isLimitedToDirectory: $this->isLimitedToDirectory(),
            isTenantAware: $this->isTenantAware(),
            tenantOwnershipRelationshipName: $this->tenantOwnershipRelationshipName(),
        );
    }

    /**
     * The media ids saved for this field on the record its form edits, read from the database rather than from the
     * field's state, which the browser holds. A form without a saved record has none, and so does an action modal
     * other than an edit action's, whose record isn't what its fields are filled from. They're read once per record
     * and request, before anything is saved, so ids being saved now never count as already saved.
     *
     * @return array<int, string>
     */
    public function getPersistedMediaIds(): array
    {
        $owner = $this->hasRelationship() ? $this->findRecordOwner() : $this->findPersistedPattern();

        if ($owner === null) {
            return [];
        }

        [$record, $path] = $owner;

        $key = $record::class . ':' . $record->getKey() . ':' . json_encode($path);

        return $this->persistedMediaIds[$key] ??= $this->hasRelationship()
            ? MediaScope::extractIds($this->readPersistedRelationshipIds())
            : $this->readPersistedAttributeIds($record, $path);
    }

    public function getEditAction(): Action
    {
        return Action::make('edit')
            ->label(trans('curator::views.picker.edit'))
            ->icon('heroicon-s-pencil')
            ->color('gray')
            ->visible(function (CuratorPicker $component) {
                return (! $component->isDisabled()) && (CuratorPlugin::get()->authorize('update'));
            })
            ->url(function (array $arguments): string {
                return App::make(MediaResource::class)
                    ->getUrl('edit', ['record' => $arguments['id']]);
            }, true);
    }

    public function getPickerAction(): Action
    {
        return Action::make('open_curator_picker')
            ->label($this->getButtonLabel())
            ->button()
            ->color($this->getColor())
            ->outlined($this->isOutlined())
            ->size($this->getSize())
            ->action(function (CuratorPicker $component, \Livewire\Component $livewire) {
                $livewire->dispatch('open-modal', id: 'curator-panel', settings: CuratorPanel::encryptSettings([
                    'acceptedFileTypes' => $component->getAcceptedFileTypes(),
                    'defaultSort' => $component->getDefaultPanelSort(),
                    'directory' => $component->getDirectory(),
                    'diskName' => $component->getDiskName(),
                    'imageCropAspectRatio' => $component->getImageCropAspectRatio(),
                    'imageResizeMode' => $component->getImageResizeMode(),
                    'imageResizeTargetWidth' => $component->getImageResizeTargetWidth(),
                    'imageResizeTargetHeight' => $component->getImageResizeTargetHeight(),
                    'isLimitedToDirectory' => $component->isLimitedToDirectory(),
                    'isTenantAware' => $component->isTenantAware(),
                    'tenantOwnershipRelationshipName' => $component->tenantOwnershipRelationshipName(),
                    'isMultiple' => $component->isMultiple(),
                    'maxItems' => $component->getMaxItems(),
                    'maxSize' => $component->getMaxSize(),
                    'maxWidth' => $component->getMaxWidth(),
                    'minSize' => $component->getMinSize(),
                    'pathGenerator' => $component->getPathGenerator(),
                    // Only string rules survive being encoded for the panel; rule objects and closures don't.
                    'rules' => array_values(array_filter($component->getValidationRules(), 'is_string')),
                    'selected' => $heldIds = $component->getMediaScope()
                        ->resolve($component->getState(), $component->getPersistedMediaIds())
                        ->map(fn (Media $media): int | string => $media->getKey())
                        ->all(),
                    'heldIds' => $heldIds,
                    'shouldPreserveFilenames' => $component->shouldPreserveFilenames(),
                    'statePath' => $component->getStatePath(),
                    'types' => $component->getAcceptedFileTypes(),
                    'visibility' => $component->getVisibility(),
                ]));
            });
    }

    public function getRemoveAction(): Action
    {
        return Action::make('remove')
            ->label(trans('curator::views.picker.remove'))
            ->icon('heroicon-s-minus-circle')
            ->color('gray')
            ->hidden(fn (CuratorPicker $component): bool => $component->isDisabled())
            ->action(function (array $arguments, CuratorPicker $component): void {
                $state = $component->getState();
                unset($state[$arguments['uuid']]);
                $component->state($state);
            });
    }

    public function getRemoveAllAction(): Action
    {
        return Action::make('removeAll')
            ->label(trans('curator::views.picker.clear'))
            ->button()
            ->outlined($this->isOutlined())
            ->color('danger')
            ->size($this->getSize())
            ->action(function (CuratorPicker $component): void {
                $component->state([]);
            });
    }

    public function getViewAction(): Action
    {
        return Action::make('view')
            ->label(trans('curator::views.picker.view'))
            ->icon('heroicon-s-eye')
            ->color('gray')
            ->url(function (array $arguments): string {
                return $arguments['url'];
            }, true);
    }

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    public function isConstrained(): bool
    {
        return $this->evaluate($this->isConstrained);
    }

    public function isLimitedToDirectory(): bool
    {
        if (! $this->getDirectory()) {
            return false;
        }

        return $this->evaluate($this->isLimitedToDirectory) ?? config('curator.is_limited_to_directory');
    }

    public function isMultiple(): bool
    {
        return $this->evaluate($this->isMultiple);
    }

    public function isTenantAware(): bool
    {
        return $this->evaluate($this->isTenantAware) ?? config('curator.is_tenant_aware');
    }

    public function tenantOwnershipRelationshipName(): string
    {
        return $this->tenantOwnershipRelationshipName ?? config('curator.tenant_ownership_relationship_name');
    }

    public function lazyLoad(bool | Closure $condition = true): static
    {
        $this->shouldLazyLoad = $condition;

        return $this;
    }

    public function limitToDirectory(bool | Closure | null $condition = true): static
    {
        $this->isLimitedToDirectory = $condition;

        return $this;
    }

    public function maxItems(int | Closure $items): static
    {
        $this->maxItems = $items;

        $this->rule('array');
        $this->rule(static function (Component $component): string {
            /** @var static $component */
            $count = $component->getMaxItems();

            return "max:{$count}";
        });

        return $this;
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function orderColumn(string $column): static
    {
        $this->orderColumn = $column;

        return $this;
    }

    public function typeColumn(string $column): static
    {
        $this->typeColumn = $column;

        return $this;
    }

    public function typeValue(string $value): static
    {
        $this->typeValue = $value;

        return $this;
    }

    public function relationship(string | Closure $relationshipName, string | Closure $titleColumnName, ?Closure $callback = null): static
    {
        $this->relationship = $relationshipName;
        $this->relationshipTitleColumnName = $titleColumnName;

        $this->loadStateFromRelationshipsUsing(static function (CuratorPicker $component, $state): void {
            if (filled($state)) {
                return;
            }

            $relationship = $component->getRelationship();

            if ($component->isMultiple()) {
                if ($relationship instanceof MorphMany) {
                    $typeColumn = $component->getTypeColumn();
                    $typeValue = $component->getTypeValue();

                    $query = $relationship->getQuery();
                    if ($typeColumn && $typeValue) {
                        $query->where($typeColumn, $typeValue);
                    }

                    // Only the ids: the records are loaded when the state is hydrated, within the field's scope,
                    // which leaves out media that was deleted or belongs to another tenant.
                    $component->state($query->pluck('media_id')->all());

                    return;
                }

                $relatedModels = $relationship->getResults();
                $component->state($relatedModels);

                return;
            }

            /** @var BelongsTo $relationship */
            $relatedModel = $relationship->getResults();

            if (! $relatedModel) {
                return;
            }

            $component->state(
                $relatedModel->getAttribute(
                    $relationship->getOwnerKeyName(),
                ),
            );
        });

        $this->saveRelationshipsUsing(static function (CuratorPicker $component, Model $record, $state) {
            $state = filled($state)
                ? $component->toStateItems($component->getMediaScope()->resolve($state, $component->getPersistedMediaIds()))
                : [];

            $relationship = $component->getRelationship();

            if (blank($state) && ! $relationship->exists()) {
                return;
            }

            if ($component->isMultiple()) {
                if ($relationship instanceof BelongsToMany) {
                    $orderColumn = $component->getOrderColumn();
                    if (in_array($orderColumn, $relationship->getPivotColumns())) {
                        $state = collect(array_values($state))->mapWithKeys(function ($item, $index) use ($orderColumn) {
                            return [$item['id'] => [$orderColumn => $index + 1]];
                        });

                        $relationship->sync($state ?? []);

                        return;
                    }

                    $state = Arr::pluck($state, 'id');
                    $relationship->sync($state ?? []);

                    return;
                }

                if ($relationship instanceof MorphMany) {
                    $orderColumn = $component->getOrderColumn();
                    $typeColumn = $component->getTypeColumn();
                    $typeValue = $component->getTypeValue();
                    $existingItems = $component->getRelationship()->where($typeColumn, $typeValue)->get()->keyBy('media_id')->toArray();
                    $newIds = collect($state)->pluck('id')->toArray();

                    $component->getRelationship()->whereNotIn('media_id', $newIds)
                        ->where($typeColumn, $typeValue)
                        ->delete();

                    $i = count($existingItems) + 1;
                    foreach ($state as $item) {
                        $itemId = $item['id'];
                        $data = [
                            'media_id' => $itemId,
                            $orderColumn => $i,
                        ];
                        if ($typeValue) {
                            $data[$typeColumn] = $typeValue;
                        }
                        if (isset($existingItems[$itemId])) {
                            $component->getRelationship()
                                ->where('media_id', $itemId)
                                ->where($typeColumn, $typeValue)
                                ->update($data);
                        } else {
                            $relationship->create($data);
                        }
                        $i++;
                    }

                    return;
                }
            }

            if (blank($state) && $relationship->exists()) {
                $relationship->disassociate();

                return;
            }

            $relationship->associate(Arr::first($state)['id']);
            $record->save();
        });

        $this->dehydrated(fn (CuratorPicker $component): bool => ! $component->isMultiple());

        return $this;
    }

    public function shouldLazyLoad(): bool
    {
        return $this->evaluate($this->shouldLazyLoad) ?? false;
    }

    public function tenantAware(bool | Closure $condition = true): static
    {
        $this->isTenantAware = $condition;

        return $this;
    }

    public function listDisplay(bool | Closure $condition = true): static
    {
        $this->shouldDisplayAsList = $condition;

        return $this;
    }

    public function shouldDisplayAsList(): bool
    {
        return $this->evaluate($this->shouldDisplayAsList) ?? false;
    }

    /**
     * The saved record the field's value belongs to, and the field's state path within it: the record of the
     * nearest container up the tree that has one of its own, such as the form or a relationship repeater's item.
     *
     * @return array{0: Model, 1: string}|null
     */
    protected function findRecordOwner(): ?array
    {
        $record = $this->getRecord();

        if (! $record instanceof Model || ! $record->exists) {
            return null;
        }

        $container = $this->getContainer();

        // Containers inherit their parent's record, so the record's own container is the highest one still holding
        // the same record.
        while (($parent = $container->getParentComponent()) !== null && $parent->getRecord() === $record) {
            $container = $parent->getContainer();
        }

        if (! $this->isFilledFromRecord($container)) {
            return null;
        }

        $path = $this->getStatePath();
        $basePath = $container->getStatePath();

        if (filled($basePath)) {
            if (! str_starts_with($path, $basePath . '.')) {
                return null;
            }

            $path = substr($path, strlen($basePath) + 1);
        }

        return [$record, $path];
    }

    /**
     * An action modal's form is given the table row or page record, but only an edit action fills its fields from
     * it.
     */
    protected function isFilledFromRecord(ComponentContainer $container): bool
    {
        $root = $container;

        while (($parent = $root->getParentComponent()) !== null) {
            $root = $parent->getContainer();
        }

        if (! str_starts_with($root->getStatePath(), 'mounted')) {
            return true;
        }

        return Str::afterLast($root->getOperation(), '.') === 'edit';
    }

    /**
     * Where the field's value is stored on the record its form edits, as a path from the record's attribute down:
     * a fixed key, `*` for any item of a repeater, or `['type' => name]` for any item of a builder whose type is the
     * field's block. It's derived from the components around the field, so only values the field itself saved are
     * read, never one stored elsewhere in the same column. Null when the path can't be told for certain, such as for
     * a simple repeater or an unknown component with a state path of its own; nothing then counts as saved.
     *
     * @return array{0: Model, 1: array<int, string|array{type: string}>}|null
     */
    protected function findPersistedPattern(): ?array
    {
        $pattern = $this->splitStatePath($this->getStatePath(isAbsolute: false));
        $container = $this->getContainer();

        while (true) {
            $component = $container->getParentComponent();
            $record = $container->getRecord();

            // Containers inherit their parent's record, so one has a record of its own when it differs from its
            // parent component's.
            if ($record !== null && ($component === null || $component->getRecord() !== $record)) {
                return $this->persistedPatternFor($record, $container, $pattern);
            }

            if ($component === null) {
                return null;
            }

            // A builder saves the items of a block hidden from the current user as they were sent, without its
            // fields' validation, so a value under a conditionally shown component may not have come from a picker.
            if ($this->hasVisibilityCondition($component)) {
                return null;
            }

            $containerPath = $container->getStatePath(isAbsolute: false);

            if ($component instanceof Repeater) {
                if ($component->isSimple() || blank($containerPath) || str_contains($containerPath, '.')) {
                    return null;
                }

                array_unshift($pattern, '*');
            } elseif ($component instanceof Block) {
                $blocks = $component->getContainer();
                $builder = $blocks->getParentComponent();

                if (
                    ! $builder instanceof Builder
                    || substr_count($containerPath, '.') !== 1
                    || ! str_ends_with($containerPath, '.data')
                    || filled($blocks->getStatePath(isAbsolute: false))
                ) {
                    return null;
                }

                array_unshift($pattern, ['type' => $component->getName()], 'data');
                $component = $builder;

                if ($this->hasVisibilityCondition($component)) {
                    return null;
                }
            } elseif (filled($containerPath)) {
                return null;
            }

            $componentRecord = $component->getRecord();

            if ($componentRecord !== null && $componentRecord !== $component->getContainer()->getRecord()) {
                return $this->persistedPatternFor($componentRecord, $component->getContainer(), $pattern);
            }

            $componentPath = $component->getStatePath(isAbsolute: false);

            if (filled($componentPath)) {
                if ($component instanceof Field && ! $component instanceof Repeater && ! $component instanceof Builder) {
                    return null;
                }

                array_unshift($pattern, ...$this->splitStatePath($componentPath));
            }

            $container = $component->getContainer();
        }
    }

    /**
     * Whether a component's visibility can change, read without evaluating it: anything other than the default
     * of always shown, including `visibleOn()`, `hiddenOn()` and conditions on other fields' state.
     */
    protected function hasVisibilityCondition(Component $component): bool
    {
        return $component->isHidden !== false || $component->isVisible !== true;
    }

    /**
     * @param  array<int, string|array{type: string}>  $pattern
     * @return array{0: Model, 1: array<int, string|array{type: string}>}|null
     */
    protected function persistedPatternFor(Model $record, ComponentContainer $container, array $pattern): ?array
    {
        if (! $record->exists || ! $this->isFilledFromRecord($container)) {
            return null;
        }

        if ($pattern === [] || ! is_string($pattern[0]) || $pattern[0] === '*') {
            return null;
        }

        return [$record, $pattern];
    }

    /**
     * @return array<int, string>
     */
    protected function splitStatePath(?string $path): array
    {
        return array_values(array_filter(explode('.', (string) $path), fn (string $segment): bool => $segment !== ''));
    }

    /**
     * The ids saved at exactly the field's path in the record attribute its state lives in.
     *
     * @param  array<int, string|array{type: string}>  $pattern
     * @return array<int, string>
     */
    protected function readPersistedAttributeIds(Model $record, array $pattern): array
    {
        $attribute = (string) array_shift($pattern);
        $value = $record->getOriginal($attribute);

        if ($pattern === []) {
            return MediaScope::extractIds($value);
        }

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        $ids = [];

        foreach ($this->findValuesAtPattern($value, $pattern) as $found) {
            array_push($ids, ...MediaScope::extractIds($found));
        }

        return array_values(array_unique($ids));
    }

    /**
     * @param  array<int, string|array{type: string}>  $pattern
     * @return array<int, mixed>
     */
    protected function findValuesAtPattern(mixed $value, array $pattern): array
    {
        if ($pattern === []) {
            return [$value];
        }

        if (! is_array($value)) {
            return [];
        }

        $segment = array_shift($pattern);

        if (is_string($segment) && $segment !== '*') {
            return array_key_exists($segment, $value) ? $this->findValuesAtPattern($value[$segment], $pattern) : [];
        }

        $found = [];

        foreach ($value as $item) {
            if (is_array($segment) && (! is_array($item) || ($item['type'] ?? null) !== $segment['type'])) {
                continue;
            }

            array_push($found, ...$this->findValuesAtPattern($item, $pattern));
        }

        return $found;
    }

    protected function readPersistedRelationshipIds(): mixed
    {
        $relationship = $this->getRelationship();

        if ($relationship instanceof BelongsTo) {
            return $this->getModelInstance()?->getRawOriginal($relationship->getForeignKeyName());
        }

        if ($relationship instanceof BelongsToMany) {
            return $relationship->allRelatedIds()->all();
        }

        if ($relationship instanceof MorphMany) {
            $query = $relationship->getQuery();

            if ($this->getTypeColumn() && $this->getTypeValue()) {
                $query->where($this->getTypeColumn(), $this->getTypeValue());
            }

            return $query->pluck('media_id')->all();
        }

        return null;
    }

    /**
     * @param  iterable<Media>  $media
     * @return array<string, array<string, mixed>>
     */
    public function toStateItems(iterable $media): array
    {
        $items = [];

        foreach ($media as $item) {
            $items[(string) Str::uuid()] = $item->toArray();
        }

        return $items;
    }
}
