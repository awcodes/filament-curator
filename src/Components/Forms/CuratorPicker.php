<?php

namespace RocketFirm\Curator\Components\Forms;

use RocketFirm\Curator\Actions\DownloadAction;
use RocketFirm\Curator\Actions\PickerAction;
use RocketFirm\Curator\Facades\Curator;
use RocketFirm\Curator\Generators\PathGenerator;
use Closure;
use Exception;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Support\Actions\Concerns\CanBeOutlined;
use Filament\Support\Actions\Concerns\HasColor;
use Filament\Support\Actions\Concerns\HasSize;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CuratorPicker extends Field
{
    use CanBeOutlined;
    use HasColor;
    use HasSize;

    protected string $view = 'curator::components.forms.picker';

    protected string|Htmlable|Closure|null $buttonLabel = null;

    protected array|Closure|null $curatorAcceptedFileTypes = null;

    protected string|Closure|null $curatorDiskName = null;

    protected string|Closure|null $curatorDirectory = null;

    protected string|Closure|null $curatorImageCropAspectRatio = null;

    protected string|Closure|null $curatorImageResizeMode = null;

    protected string|Closure|null $curatorImageResizeTargetHeight = null;

    protected string|Closure|null $curatorImageResizeTargetWidth = null;

    protected string|null $curatorPathGenerator = null;

    protected int|Closure|null $curatorMaxSize = null;

    protected int|Closure|null $curatorMinSize = null;

    protected bool|Closure|null $curatorShouldPreserveFilenames = null;

    protected string|Closure|null $curatorVisibility = null;

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

        $this->buttonLabel = __('curator::views.picker.button');
        $this->size = 'md';
        $this->color = 'primary';
        $this->isOutlined = true;

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
                $media = app('curator')->getMedia($state);
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
        ]);

        $this->registerActions([
            PickerAction::make(),
            DownloadAction::make(),
        ]);
    }

    public function acceptedFileTypes(array|Arrayable|Closure $types): static
    {
        $this->curatorAcceptedFileTypes = $types;

        return $this;
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

    public function directory(Closure|string $directory): static
    {
        $this->curatorDirectory = $directory;

        return $this;
    }

    public function disk(string|Closure $name): static
    {
        $this->curatorDiskName = $name;

        return $this;
    }

    public function getAcceptedFileTypes(): ?array
    {
        $types = $this->evaluate($this->curatorAcceptedFileTypes) ?? app('curator')->getAcceptedFileTypes();

        if ($types instanceof Arrayable) {
            $types = $types->toArray();
        }

        return $types;
    }

    public function getButtonLabel(): string
    {
        return $this->evaluate($this->buttonLabel);
    }

    /**
     * @deprecated
     */
    public function getCurrentItem(): Model|Collection|null
    {
        if (!filled($this->getState())) {
            return null;
        }

        return Curator::getMediaModel()::where('id', $this->getState())->first();
    }

    public function getDirectory(): string
    {
        return $this->evaluate($this->curatorDirectory) ?? app('curator')->getDirectory();
    }

    public function getDiskName(): string
    {
        return $this->evaluate($this->curatorDiskName) ?? app('curator')->getDiskName();
    }

    public function getImageCropAspectRatio(): ?string
    {
        return $this->evaluate($this->curatorImageCropAspectRatio) ?? app('curator')->getImageCropAspectRatio();
    }

    public function getImageResizeMode(): ?string
    {
        return $this->evaluate($this->curatorImageResizeMode) ?? app('curator')->getImageResizeMode();
    }

    public function getImageResizeTargetHeight(): ?string
    {
        return $this->evaluate($this->curatorImageResizeTargetHeight) ?? app('curator')->getImageResizeTargetHeight();
    }

    public function getImageResizeTargetWidth(): ?string
    {
        return $this->evaluate($this->curatorImageResizeTargetWidth) ?? app('curator')->getImageResizeTargetWidth();
    }

    public function getMaxItems(): ?int
    {
        return $this->evaluate($this->maxItems);
    }

    public function getMaxSize(): ?int
    {
        return $this->evaluate($this->curatorMaxSize) ?? app('curator')->getMaxSize();
    }

    public function getMinSize(): ?int
    {
        return $this->evaluate($this->curatorMinSize) ?? app('curator')->getMinSize();
    }

    public function getOrderColumn(): string
    {
        return $this->orderColumn ?? 'order';
    }

    public function getPathGenerator(): PathGenerator|string|null
    {
        return $this->curatorPathGenerator ?? app('curator')->getPathGenerator();
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

    public function getVisibility(): string
    {
        return $this->evaluate($this->curatorVisibility) ?? app('curator')->getVisibility();
    }

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    public function imageCropAspectRatio(string|Closure $ratio): static
    {
        $this->curatorImageCropAspectRatio = $ratio;

        return $this;
    }

    public function imageResizeMode(string|Closure $mode): static
    {
        $this->curatorImageResizeMode = $mode;

        return $this;
    }

    public function imageResizeTargetHeight(string|Closure $height): static
    {
        $this->curatorImageResizeTargetHeight = $height;

        return $this;
    }

    public function imageResizeTargetWidth(string|Closure $width): static
    {
        $this->curatorImageResizeTargetWidth = $width;

        return $this;
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

        return $this->evaluate($this->isLimitedToDirectory) ?? app('curator')->isLimitedToDirectory();
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

    public function maxSize(int|Closure $size): static
    {
        $this->curatorMaxSize = $size;

        return $this;
    }

    public function minSize(int|Closure $size): static
    {
        $this->curatorMinSize = $size;

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

    public function pathGenerator(PathGenerator|string|null $generator): static
    {
        $this->curatorPathGenerator = $generator;

        return $this;
    }

    public function preserveFilenames(bool|Closure|null $condition = true): static
    {
        $this->curatorShouldPreserveFilenames = $condition;

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

            $relationship = $component->getRelationship();

            if (blank($state) && !$relationship->exists()) {
                return;
            }

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

            if (blank($state) && $relationship->exists()) {
                $relationship->dissociate();
                return;
            }

            $relationship->associate(Arr::first($state)['id']);
            $record->save();
        });

        $this->dehydrated(fn(CuratorPicker $component): bool => !$component->isMultiple());

        return $this;
    }

    public function shouldPreserveFilenames(): bool
    {
        return $this->evaluate($this->curatorShouldPreserveFilenames) ?? app('curator')->shouldPreserveFilenames();
    }
}
