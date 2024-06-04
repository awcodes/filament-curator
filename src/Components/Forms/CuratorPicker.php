<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Concerns\CanGeneratePaths;
use Awcodes\Curator\Concerns\CanUploadFiles;
use Awcodes\Curator\Resources\MediaResource;
use Closure;
use Exception;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\HasSize;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
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

    protected ?string $typeColumn = null;

    protected ?string $typeValue = null;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->buttonLabel(trans('curator::views.picker.button'))
            ->size(ActionSize::Medium)
            ->color('gray')
            ->outlined();

        $this->afterStateHydrated(static function (CuratorPicker $component, array | int | string | null $state): void {
            if (blank($state)) {
                $component->state([]);

                return;
            }

            $items = [];

            $state = is_array($state) ? array_values($state) : $state;

            if (is_array($state) && isset($state[0]['id'])) {
                $media = $state;
            } elseif (isset($state['id'])) {
                $media = [$state];
            } else {
                $state = Arr::wrap($state);
                $media = get_media_items($state)->toArray();
            }

            foreach ($media as $itemData) {
                $items[(string) Str::uuid()] = $itemData;
            }

            $component->state($items);
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

        $this->dehydrateStateUsing(function (CuratorPicker $component, $state) {
            if (! filled($state)) {
                return null;
            }

            $state = collect($state)->pluck('id')->toArray();

            if (count($state) === 1 && is_array($state) && ! $component->isMultiple()) {
                $state = $state[0];
            }

            return $state;
        });

        $this->registerListeners([
            'curator::updateState' => [
                function (CuratorPicker $component, string $statePath, array $arguments): void {
                    if ($component->getStatePath() !== $statePath) {
                        return;
                    }

                    $items = [];

                    $state = array_values($arguments['media']);

                    foreach ($state as $itemData) {
                        $items[(string) Str::uuid()] = $itemData;
                    }

                    $component->state($items);
                },
            ]
        ]);

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
            ->action(function (array $arguments, CuratorPicker $component): StreamedResponse {
                $item = $component->getState()[$arguments['uuid']];

                return Storage::disk($item['disk'])->download($item['path']);
            });
    }

    public function getEditAction(): Action
    {
        return Action::make('edit')
            ->label(trans('curator::views.picker.edit'))
            ->icon('heroicon-s-pencil')
            ->color('gray')
            ->hidden(fn (CuratorPicker $component): bool => $component->isDisabled())
            ->url(function (array $arguments): string {
                return App::make(MediaResource::class)
                    ->getUrl('edit', ['record' => $arguments['id']]);
            }, true);
    }

    public function getPickerAction(): Action
    {
        return Action::make('launchPanel')
            ->label($this->getButtonLabel())
            ->button()
            ->color($this->getColor())
            ->outlined($this->isOutlined())
            ->size($this->getSize())
            ->modalHeading(false)
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalWidth(MaxWidth::Screen)
            ->modalContent(function (CuratorPicker $component): View {
                return view('curator::components.modals.curator-panel', [
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
                        'maxWidth' => $component->getMaxWidth(),
                        'minSize' => $component->getMinSize(),
                        'pathGenerator' => $component->getPathGenerator(),
                        'rules' => $component->getValidationRules(),
                        'selected' => (array) $component->getState(),
                        'shouldPreserveFilenames' => $component->shouldPreserveFilenames(),
                        'statePath' => $component->getStatePath(),
                        'visibility' => $component->getVisibility(),
                    ]
                ]);
            })
            ->action(fn () => null);
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

                    $query = $relationship->with('media');
                    if ($typeColumn && $typeValue) {
                        $query->where($typeColumn, $typeValue);
                    }
                    $relatedMediaItems = $query->get();

                    $relatedMedia = $relatedMediaItems->map(function ($item) {
                        return $item->media->toArray();
                    })->toArray();

                    $component->state($relatedMedia);
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
                    $existingItems = $relationship->where($typeColumn, $typeValue)->get()->keyBy('media_id')->toArray();
                    $newIds = collect($state)->pluck('id')->toArray();

                    $relationship->whereNotIn('media_id', $newIds)
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
                            $relationship->where('media_id', $itemId)->update($data);
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

    public function getTypeColumn(): string
    {
        return $this->typeColumn ?? 'type';
    }

    public function getTypeValue(): ?string
    {
        return $this->typeValue ?? null;
    }
}
