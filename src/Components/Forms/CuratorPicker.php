<?php

namespace Awcodes\Curator\Components\Forms;

use Awcodes\Curator\Actions\DownloadAction;
use Awcodes\Curator\Actions\PickerAction;
use Awcodes\Curator\Concerns\CanGeneratePaths;
use Awcodes\Curator\Concerns\CanUploadFiles;
use function Awcodes\Curator\get_media_items;
use Closure;
use Exception;
use Filament\Actions\Concerns\CanBeOutlined;
use Filament\Actions\Concerns\HasSize;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasColor;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CuratorPicker extends Field
{
    use CanBeOutlined;
    use HasColor;
    use HasSize;
    use CanUploadFiles;
    use CanGeneratePaths;

    protected string $view = 'curator::components.forms.picker';

    protected string | Htmlable | Closure | null $buttonLabel = null;

    protected bool | Closure | null $isConstrained = false;

    protected bool | null $isLimitedToDirectory = null;

    protected bool | Closure | null $isMultiple = false;

    protected int | Closure | null $maxItems = null;

    protected string | null $orderColumn = null;

    protected string | Closure | null $relationship = null;

    protected string | Closure | null $relationshipTitleColumnName = null;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->buttonLabel = __('curator::views.picker.button');
        $this->size = 'md';
        $this->color = 'primary';
        $this->isOutlined = true;

        $this->afterStateHydrated(static function (CuratorPicker $component, array | int | null $state): void {
            $items = [];

            if (! filled($state)) {
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
                $items[(string) Str::uuid()] = $itemData;
            }

            $component->state($items);
        });

        $this->afterStateUpdated(static function (CuratorPicker $component, array | int | null $state): void {
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
            'picker::moveItems' => [
                function (CuratorPicker $component, string $statePath, array $uuids): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $items = array_merge(array_flip($uuids), $component->getState());

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, $items);
                },
            ],
            'picker::clearItems' => [
                function (CuratorPicker $component, string $statePath): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }

                    $livewire = $component->getLivewire();
                    data_set($livewire, $statePath, []);
                },
            ],
        ]);

        $this->registerActions([
            PickerAction::make(),
            DownloadAction::make(),
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

    public function getRelationship(): BelongsTo | BelongsToMany | \Znck\Eloquent\Relations\BelongsToThrough | null
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

        $this->dehydrated(fn (CuratorPicker $component): bool => ! $component->isMultiple());

        return $this;
    }
}
