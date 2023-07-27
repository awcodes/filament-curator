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
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function Awcodes\Curator\get_media_items;

class CuratorPicker extends Field
{
    use CanBeOutlined;
    use HasColor;
    use HasSize;
    use CanUploadFiles;
    use CanGeneratePaths;

    protected string $view = 'curator::components.forms.picker';

    protected string|Htmlable|Closure|null $buttonLabel = null;

    protected bool|Closure|null $isConstrained = false;

    protected bool|null $isLimitedToDirectory = null;

    protected bool|Closure|null $isMultiple = false;

    protected int|Closure|null $maxItems = null;

    protected string|null $orderColumn = null;

    protected string|Closure|null $relationship = null;

    protected string|Closure|null $relationshipTitleColumnName = null;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->buttonLabel(__('curator::views.picker.button'))
            ->size('md')
            ->color('primary')
            ->outlined();

        $this->afterStateHydrated(static function (CuratorPicker $component, array|int|null $state): void {
            $items = [];

            if (!filled($state)) {
                $component->state($items);

                return;
            }

            if (is_array($state) && isset($state[0]['id'])) {
                $media = $state;
            } elseif (isset($state['id'])) {
                $media = [$state];
            } else {
                $state = Arr::wrap($state);
                $media = get_media_items($state);
            }

            foreach ($media as $itemData) {
                $items[(string)Str::uuid()] = $itemData;
            }

            $component->state($items);
        });

        $this->afterStateUpdated(static function (CuratorPicker $component, array|int|null $state): void {
            $items = [];

            $state = array_values($state);

            foreach ($state as $itemData) {
                $items[(string)Str::uuid()] = $itemData;
            }

            $component->state($items);
        });

        $this->dehydrateStateUsing(function (CuratorPicker $component, $state) {
            if (!filled($state)) {
                return null;
            }

            $state = collect($state)->pluck('id')->toArray();

            if (count($state) === 1 && is_array($state) && !$component->isMultiple()) {
                $state = $state[0];
            }

            return $state;
        });

        $this->registerActions([
            fn(CuratorPicker $component): Action => $component->getDownloadAction(),
            fn(CuratorPicker $component): Action => $component->getEditAction(),
            fn(CuratorPicker $component): Action => $component->getRemoveAction(),
            fn(CuratorPicker $component): Action => $component->getRemoveAllAction(),
            fn(CuratorPicker $component): Action => $component->getReorderAction(),
            fn(CuratorPicker $component): Action => $component->getViewAction(),
            fn(CuratorPicker $component): Action => $component->getPickerAction(),
        ]);
    }

    public function buttonLabel(string|Htmlable|Closure $label): static
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function constrained(bool|Closure|null $condition = true): static
    {
        $this->isConstrained = $condition;

        return $this;
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

    public function getRelationship(): BelongsTo|BelongsToMany|\Znck\Eloquent\Relations\BelongsToThrough|null
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
            ->label(__('curator::views.picker.reorder'))
            ->icon('heroicon-s-arrows-up-down')
            ->iconButton()
            ->size('xs')
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->extraAttributes(['style' => 'cursor: move;'])
            ->action(function (array $arguments, CuratorPicker $component): void {
                $items = [
                    ...array_flip($arguments['items']),
                    ...$component->getState(),
                ];

                $component->state($items);
            });
    }

    public function getDownloadAction(): Action
    {
        return Action::make('download')
            ->label(__('curator::views.picker.download'))
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
            ->label(__('curator::views.picker.edit'))
            ->icon('heroicon-s-pencil')
            ->color('gray')
            ->hidden(fn(CuratorPicker $component): bool => $component->isDisabled())
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
            ->modalWidth('screen')
            ->modalFooterActions(fn() => [])
            ->modalHeading(__('curator::views.panel.heading'))
            ->modalContent(static function (CuratorPicker $component) {
                return View::make('curator::components.actions.picker-action', [
                    'acceptedFileTypes' => $component->getAcceptedFileTypes(),
                    'directory' => $component->getDirectory(),
                    'diskName' => $component->getDiskName(),
                    'imageCropAspectRatio' => $component->getImageCropAspectRatio(),
                    'imageResizeMode' => $component->getImageResizeMode(),
                    'imageResizeTargetWidth' => $component->getImageResizeTargetWidth(),
                    'imageResizeTargetHeight' => $component->getImageResizeTargetHeight(),
                    'isLimitedToDirectory' => $component->isLimitedToDirectory(),
                    'isMultiple' => $component->isMultiple(),
                    'maxItems' => $component->getMaxItems(),
                    'maxSize' => $component->getMaxSize(),
                    'maxWidth' => $component->getMaxWidth(),
                    'minSize' => $component->getMinSize(),
                    'modalId' => $component->getLivewire()->getId() . '-form-component-action',
                    'pathGenerator' => $component->getPathGenerator(),
                    'rules' => $component->getValidationRules(),
                    'selected' => array_values((array)$component->getState()),
                    'shouldPreserveFilenames' => $component->shouldPreserveFilenames(),
                    'statePath' => $component->getStatePath(),
                    'visibility' => $component->getVisibility(),
                ]);
            });
    }

    public function getRemoveAction(): Action
    {
        return Action::make('remove')
            ->label(__('curator::views.picker.remove'))
            ->icon('heroicon-s-minus-circle')
            ->color('gray')
            ->hidden(fn(CuratorPicker $component): bool => $component->isDisabled())
            ->action(function (array $arguments, CuratorPicker $component): void {
                $state = $component->getState();
                unset($state[$arguments['uuid']]);
                $component->state($state);
            });
    }

    public function getRemoveAllAction(): Action
    {
        return Action::make('removeAll')
            ->label(__('curator::views.picker.clear'))
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
            ->label(__('curator::views.picker.view'))
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
        if (!$this->getDirectory()) {
            return false;
        }

        return $this->evaluate($this->isLimitedToDirectory) ?? config('curator.is_limited_to_directory');
    }

    public function isMultiple(): bool
    {
        return $this->evaluate($this->isMultiple);
    }

    public function limitToDirectory(bool|Closure|null $condition = true): static
    {
        $this->isLimitedToDirectory = $condition;

        return $this;
    }

    public function maxItems(int|Closure $items): static
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

    public function multiple(bool|Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function orderColumn(string $column): static
    {
        $this->orderColumn = $column;

        return $this;
    }

    public function relationship(string|Closure $relationshipName, string|Closure $titleColumnName, ?Closure $callback = null): static
    {
        $this->relationship = $relationshipName;
        $this->relationshipTitleColumnName = $titleColumnName;

        $this->loadStateFromRelationshipsUsing(static function (CuratorPicker $component, $state): void {
            if (filled($state)) {
                return;
            }

            $relationship = $component->getRelationship();

            if ($component->isMultiple()) {
                $relatedModels = $relationship->getResults();

                $component->state($relatedModels);

                return;
            }

            /** @var BelongsTo $relationship */
            $relatedModel = $relationship->getResults();

            if (!$relatedModel) {
                return;
            }

            $component->state(
                $relatedModel->getAttribute(
                    $relationship->getOwnerKeyName(),
                ),
            );
        });

        $this->saveRelationshipsUsing(static function (CuratorPicker $component, Model $record, $state) {
            if (blank($state)) {
                return;
            }

            $relationship = $component->getRelationship();

            if ($component->isMultiple()) {
                if (
                    ($relationship instanceof BelongsToMany) &&
                    in_array($component->getOrderColumn(), $relationship->getPivotColumns())
                ) {
                    $orderColumn = $component->getOrderColumn();
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

            $relationship->associate(Arr::first($state)['id']);
            $record->save();
        });

        $this->dehydrated(fn(CuratorPicker $component): bool => !$component->isMultiple());

        return $this;
    }
}
