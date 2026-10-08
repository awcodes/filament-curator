<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Concerns\CanGeneratePaths;
use Awcodes\Curator\Concerns\CanUploadFiles;
use Awcodes\Curator\Config\CuratorManager;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\MediaResource;
use Awcodes\Curator\Support\MediaScope;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\HasSize;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ReflectionMethod;
use ReflectionProperty;
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

    protected string | Closure | null $relationship = null;

    protected string | Closure | null $relationshipTitleColumnName = null;

    protected bool | Closure | null $shouldDisplayAsList = null;

    protected string | Closure | null $defaultPanelSort = null;

    protected string | Closure | null $typeColumn = null;

    protected string | Closure | null $typeValue = null;

    /** @var array<string, array<int, string>> */
    protected array $persistedMediaIds = [];

    /** @throws Exception */
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->buttonLabel(trans('curator::views.picker.button'))
            ->size(Size::Medium)
            ->color('gray')
            ->outlined();

        $this->afterStateHydrated(static function (CuratorPicker $component, array | int | string | null $state): void {
            if (blank($state)) {
                $component->state([]);

                return;
            }

            // The value is loaded again within the field's scope, so an id it wouldn't list never loads. Media already
            // saved on the record keeps loading as long as it exists and belongs to the current tenant.
            $component->state($component->toStateItems($component->getMediaScope()->resolve($state, $component->getPersistedMediaIds())));
        });

        $this->afterStateUpdated(function (CuratorPicker $component, array | int | null $state): void {
            if (! filled($state)) {
                $component->state([]);
            }

            $items = [];

            $state = array_values($state);

            foreach ($state as $itemData) {
                $items[(string) Str::uuid()] = $itemData;
            }

            $component->state($items);
        });

        $this->dehydrateStateUsing(function (CuratorPicker $component, mixed $state): int | string | array | null {
            $ids = $component->getMediaScope()->resolve($state, $component->getPersistedMediaIds())
                ->map(fn (Media $media): int | string => $media->getKey())
                ->all();

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

    #[ExposedLivewireMethod]
    public function updateState(array $arguments): void
    {
        if ($this->getStatePath() !== ($arguments['statePath'] ?? null)) {
            return;
        }

        $media = $this->getMediaScope()->resolve(
            array_filter(Arr::wrap($arguments['media'] ?? []), is_array(...)),
            $this->getPersistedMediaIds(),
        );

        if (! $this->isMultiple()) {
            $media = $media->take(1);
        }

        $this->state($this->toStateItems($media));
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
            tenantOwnershipRelationshipName: $this->getTenantOwnershipRelationshipName(),
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
            ->icon(Heroicon::ArrowsPointingOut)
            ->iconButton()
            ->size('xs')
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->extraAttributes(['style' => 'cursor: move; transform: rotate(45deg);'])
            ->action(function (array $arguments, CuratorPicker $component): void {
                $items = [
                    ...array_flip($arguments['items']),
                    ...$component->getRawState(),
                ];

                $component->rawState($items);

                $component->callAfterStateUpdated();

                $component->partiallyRender();
            });
    }

    public function getDownloadAction(): Action
    {
        return Action::make('download')
            ->label(trans('curator::views.picker.download'))
            ->icon(Heroicon::ArrowDownTray)
            ->color('gray')
            ->action(function (array $arguments, CuratorPicker $component): ?StreamedResponse {
                $record = $component->resolveAuthorizedMedia($arguments['uuid'] ?? null, 'view');

                if (! $record instanceof Media) {
                    return null;
                }

                return Storage::disk($record->disk)->download($record->path);
            });
    }

    public function getEditAction(): Action
    {
        return Action::make('edit')
            ->label(trans('curator::views.picker.edit'))
            ->icon(Heroicon::Pencil)
            ->color('gray')
            ->hidden(fn (CuratorPicker $component): bool => $component->isDisabled())
            ->url(fn (array $arguments): string => App::make(MediaResource::class)
                ->getUrl('edit', ['record' => $arguments['id']]), true);
    }

    public function getPickerAction(): Action
    {
        return Action::make('launchPanel')
            ->label($this->getButtonLabel())
            ->button()
            ->color($this->getColor())
            ->outlined($this->isOutlined())
            ->size($this->getSize())
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalWidth(Width::Screen)
            ->modalCloseButton(false)
            ->modalContent(fn (CuratorPicker $component): View => view('curator::components.modals.curator-panel', [
                'key' => $component->getKey(),
                'settings' => [
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
                    'tenantOwnershipRelationshipName' => $component->getTenantOwnershipRelationshipName(),
                    'isMultiple' => $component->isMultiple(),
                    'maxItems' => $component->getMaxItems(),
                    'maxSize' => $component->getMaxSize(),
                    'minSize' => $component->getMinSize(),
                    'pathGenerator' => $component->getPathGenerator(),
                    'rules' => $component->getValidationRules(),
                    'selected' => (array) $component->getState(),
                    'shouldPreserveFilenames' => $component->shouldPreserveFilenames(),
                    'statePath' => $component->getStatePath(),
                    'visibility' => $component->getVisibility(),
                ],
            ]))
            ->action(fn (): null => null);
    }

    public function getRemoveAction(): Action
    {
        return Action::make('remove')
            ->label(trans('curator::views.picker.remove'))
            ->icon(Heroicon::MinusCircle)
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
            ->icon(Heroicon::Eye)
            ->color('gray')
            ->url(fn (array $arguments): string => $arguments['url'], true);
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
        return $this->evaluate($this->isLimitedToDirectory) ?? config('curator.features.directory_restriction');
    }

    public function isMultiple(): bool
    {
        return $this->evaluate($this->isMultiple);
    }

    public function isTenantAware(): bool
    {
        return $this->evaluate($this->isTenantAware) ?? config('curator.features.tenancy.enabled');
    }

    public function getTenantOwnershipRelationshipName(): ?string
    {
        return $this->tenantOwnershipRelationshipName ?? config('curator.features.tenancy.relationship_name');
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

    public function typeColumn(string | Closure $column): static
    {
        $this->typeColumn = $column;

        return $this;
    }

    public function typeValue(string | Closure $value): static
    {
        $this->typeValue = $value;

        return $this;
    }

    public function relationship(string | Closure $relationshipName, string | Closure $titleColumnName): static
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
                    // Only the ids, in order: the records are loaded when the state is hydrated, within the field's
                    // scope, which leaves out media that was deleted or belongs to another tenant.
                    $component->state($relationship
                        ->where($component->getTypeColumn(), $component->getTypeValue())
                        ->orderBy($component->getOrderColumn())
                        ->pluck('media_id')
                        ->all());

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

        $this->saveRelationshipsUsing(static function (CuratorPicker $component, Model $record, $state): void {
            $relationship = $component->getRelationship();

            $state = filled($state) ? $component->toStateItems($component->getMediaScope()->resolve($state, $component->getPersistedMediaIds())) : [];

            if (blank($state) && ! $relationship->exists()) {
                return;
            }

            if ($component->isMultiple()) {
                if ($relationship instanceof BelongsToMany) {
                    $orderColumn = $component->getOrderColumn();
                    if (in_array($orderColumn, $relationship->getPivotColumns())) {
                        $state = collect(array_values($state))->mapWithKeys(fn (array $item, $index): array => [$item['id'] => [$orderColumn => $index + 1]]);

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

                    $i = 1;
                    foreach ($state as $item) {
                        $itemId = $item['id'];
                        $data = [
                            'media_id' => $itemId,
                            $orderColumn => $i,
                            $typeColumn => $typeValue,
                        ];
                        if (isset($existingItems[$itemId])) {
                            $component->getRelationship()->where('media_id', $itemId)->where($typeColumn, $typeValue)->update($data);
                        } else {
                            $component->getRelationship()->create($data);
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

    public function getTypeColumn(): string
    {
        return $this->evaluate($this->typeColumn) ?? 'type';
    }

    public function getTypeValue(): ?string
    {
        return $this->evaluate($this->typeValue) ?? null;
    }

    /**
     * The field state, including each item's disk and path, comes from the client,
     * so only the item's id is used. The record is reloaded within the field's
     * scope, as the panel's queries are, and checked against the Media policy.
     */
    protected function resolveAuthorizedMedia(mixed $uuid, string $ability): ?Media
    {
        if (! is_string($uuid) && ! is_int($uuid)) {
            return null;
        }

        $state = $this->getState();

        $id = is_array($state) ? ($state[$uuid]['id'] ?? null) : null;

        if (blank($id) || ! is_scalar($id)) {
            return null;
        }

        $record = $this->getMediaScope()->resolve([$id], $this->getPersistedMediaIds())->first();

        if (! $record instanceof Media) {
            return null;
        }

        $resource = App::make(MediaResource::class);

        return $resource::can($ability, $record) ? $record : null;
    }

    /**
     * The saved record the field's value belongs to, and the field's state path within it: the nearest schema or
     * component up the tree that holds a record of its own, such as the form, a relationship repeater's item or a
     * relationship group.
     *
     * @return array{0: Model, 1: string}|null
     */
    protected function findRecordOwner(): ?array
    {
        $schema = $this->getContainer();

        while ($schema instanceof Schema) {
            $record = $schema->getRecord(withParentComponentRecord: false);
            $basePath = $schema->getStatePath();

            if ($record === null) {
                $component = $schema->getParentComponent();
                $record = $component?->getRecord(withContainerRecord: false);
                $basePath = $component?->getStatePath();
            }

            if ($record !== null) {
                if (! $record instanceof Model || ! $record->exists || ! $this->isFilledFromRecord($schema)) {
                    return null;
                }

                $path = (string) $this->getStatePath();

                if (filled($basePath)) {
                    if (! str_starts_with($path, $basePath . '.')) {
                        return null;
                    }

                    $path = substr($path, strlen($basePath) + 1);
                }

                return [$record, $path];
            }

            $schema = $schema->getParentComponent()?->getContainer();
        }

        return null;
    }

    /**
     * An action modal's schema is given the table row or page record, but only an edit action fills its fields
     * from it.
     */
    protected function isFilledFromRecord(Schema $schema): bool
    {
        $root = $schema;

        while (($parent = $root->getParentComponent()?->getContainer()) instanceof Schema) {
            $root = $parent;
        }

        if (! str_starts_with((string) $root->getStatePath(), 'mountedActions.')) {
            return true;
        }

        return Str::afterLast($root->getOperation(), '.') === 'edit';
    }

    /**
     * Where the field's value is stored on the record its form edits, as a path from the record's attribute down:
     * a fixed key, `*` for any item of a repeater, or `['block' => name]` for any item of a builder whose type is
     * that block. It's derived from the components around the field, so only values the field itself saved are
     * read, never one stored elsewhere in the same column. Null when the path can't be told for certain, such as
     * for a simple repeater or an unknown component with a state path of its own; nothing then counts as saved.
     *
     * @return array{0: Model, 1: array<int, string|array{block: string}>}|null
     */
    protected function findPersistedPattern(): ?array
    {
        $pattern = $this->splitStatePath($this->getStatePath(isAbsolute: false));
        $schema = $this->getContainer();

        while (true) {
            if (! $this->isUnconditionallyVisible($schema)) {
                return null;
            }

            if ($schema->getRecord(withParentComponentRecord: false) !== null) {
                return $this->persistedPatternFor($schema->getRecord(withParentComponentRecord: false), $schema, $pattern);
            }

            $component = $schema->getParentComponent();
            $schemaPath = $schema->getStatePath(isAbsolute: false);

            if ($component instanceof Repeater) {
                if ($component->isSimple() || blank($schemaPath) || str_contains($schemaPath, '.')) {
                    return null;
                }

                array_unshift($pattern, '*');
            } elseif ($component instanceof Block) {
                $blocks = $component->getContainer();
                $builder = $blocks->getParentComponent();

                if (! $builder instanceof Builder || blank($schemaPath) || ! str_ends_with($schemaPath, '.data') || filled($blocks->getStatePath(isAbsolute: false))) {
                    return null;
                }

                // A builder saves the items of a block hidden from the current user as they were sent, unchecked,
                // so a picker in a block that isn't always visible can't trust what its column holds.
                if (! $this->isUnconditionallyVisible($component) || ! $this->isUnconditionallyVisible($blocks)) {
                    return null;
                }

                array_unshift($pattern, ['block' => $component->getName()], 'data');
                $component = $builder;
            } elseif (filled($schemaPath)) {
                return null;
            }

            if (! $component instanceof Component || ! $this->isUnconditionallyVisible($component)) {
                return null;
            }

            $record = $component->getRecord(withContainerRecord: false);

            if ($record !== null) {
                return $this->persistedPatternFor($record, $component->getContainer(), $pattern);
            }

            $componentPath = $component->getStatePath(isAbsolute: false);

            if (filled($componentPath)) {
                if ($component instanceof Field && ! $component instanceof Repeater && ! $component instanceof Builder) {
                    return null;
                }

                array_unshift($pattern, ...$this->splitStatePath($componentPath));
            }

            $schema = $component->getContainer();
        }
    }

    /**
     * Whether a schema or component is visible whoever opens the form and whatever its state: no `hidden()` or
     * `visible()` condition, which includes `hiddenOn()`, `visibleOn()`, `whenTruthy()` and the like, and no
     * `isHidden()` of its own. Read without evaluating anything.
     */
    protected function isUnconditionallyVisible(Schema | Component $node): bool
    {
        $hidden = property_exists($node, 'isHidden') ? (new ReflectionProperty($node, 'isHidden'))->getValue($node) : false;
        $visible = property_exists($node, 'isVisible') ? (new ReflectionProperty($node, 'isVisible'))->getValue($node) : true;

        if ($hidden !== false || $visible !== true) {
            return false;
        }

        $declaringClass = (new ReflectionMethod($node, 'isHidden'))->getDeclaringClass()->getName();

        return in_array($declaringClass, [Component::class, Schema::class], true);
    }

    /**
     * @param  array<int, string|array{block: string}>  $pattern
     * @return array{0: Model, 1: array<int, string|array{block: string}>}|null
     */
    protected function persistedPatternFor(mixed $record, Schema $schema, array $pattern): ?array
    {
        if (! $record instanceof Model || ! $record->exists || ! $this->isFilledFromRecord($schema)) {
            return null;
        }

        if ($pattern === [] || ! is_string($pattern[0])) {
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
     * @param  array<int, string|array{block: string}>  $pattern
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

        return array_values(array_unique(array_merge([], ...array_map(
            MediaScope::extractIds(...),
            $this->findValuesAt($value, $pattern),
        ))));
    }

    /**
     * @param  array<int, string|array{block: string}>  $pattern
     * @return array<int, mixed>
     */
    protected function findValuesAt(mixed $value, array $pattern): array
    {
        if ($pattern === []) {
            return [$value];
        }

        if (! is_array($value)) {
            return [];
        }

        $segment = array_shift($pattern);

        if (is_string($segment) && $segment !== '*') {
            return array_key_exists($segment, $value) ? $this->findValuesAt($value[$segment], $pattern) : [];
        }

        $found = [];

        foreach ($value as $item) {
            if (is_array($segment) && (! is_array($item) || ($item['type'] ?? null) !== $segment['block'])) {
                continue;
            }

            array_push($found, ...$this->findValuesAt($item, $pattern));
        }

        return $found;
    }

    protected function readPersistedRelationshipIds(): mixed
    {
        $relationship = $this->getRelationship();
        $record = $this->getModelInstance();

        return match (true) {
            $relationship instanceof BelongsTo => $record?->getRawOriginal($relationship->getForeignKeyName()),
            $relationship instanceof BelongsToMany => $relationship->allRelatedIds()->all(),
            $relationship instanceof MorphMany => $relationship
                ->where($this->getTypeColumn(), $this->getTypeValue())
                ->pluck('media_id')
                ->all(),
            default => null,
        };
    }

    /**
     * @param  iterable<Media>  $media
     * @return array<string, array<string, mixed>>
     */
    protected function toStateItems(iterable $media): array
    {
        $items = [];

        foreach ($media as $item) {
            $items[(string) Str::uuid()] = $item->toArray();
        }

        return $items;
    }

    protected function getUploadDefaults(): ?CuratorManager
    {
        return app(CuratorManager::class);
    }
}
