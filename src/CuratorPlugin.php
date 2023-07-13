<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Resources\MediaResource;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Session;

class CuratorPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string|null $navigationGroup = null;

    protected string $navigationIcon = 'heroicon-o-photo';

    protected int|null $navigationSort = null;

    protected string|Closure $pluralResourceLabel = 'Media';

    protected string|null $resource = null;

    protected string|Closure $resourceLabel = 'Media';

    protected bool|Closure|null $tableHasGridLayout = true;

    protected bool|Closure|null $tableHasIconActions = false;

    public function getId(): string
    {
        return 'awcodes/curator';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                $this->getResource()
            ]);
    }

    public function boot(Panel $panel): void
    {
        $this->shouldTableHaveGridLayout();
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }

    public function getResource(): string
    {
        return $this->resource ?? MediaResource::class;
    }

    public function getResourceLabel(): string
    {
        return $this->evaluate($this->resourceLabel);
    }

    public function getPluralResourceLabel(): string
    {
        return $this->evaluate($this->pluralResourceLabel);
    }

    public function getNavigationGroup(): string|null
    {
        return $this->navigationGroup;
    }

    public function getNavigationIcon(): string|null
    {
        return $this->navigationIcon;
    }

    public function getNavigationSort(): int|null
    {
        return $this->navigationSort;
    }

    public function navigationGroup(string|null $group = null): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationIcon(string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationSort(int $order): static
    {
        $this->navigationSort = $order;

        return $this;
    }

    public function pluralResourceLabel(string|Closure $label): static
    {
        $this->pluralResourceLabel = $label;

        return $this;
    }

    public function resource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function resourceLabel(string|Closure $label): static
    {
        $this->resourceLabel = $label;

        return $this;
    }

    public function shouldTableHaveGridLayout(): string
    {
        if (!Session::has('tableLayout')) {
            Session::put('tableLayout', $this->evaluate($this->tableHasGridLayout));
        }

        return Session::get('tableLayout');
    }

    public function shouldTableHaveIconActions(): string
    {
        return $this->evaluate($this->tableHasIconActions);
    }

    public function tableHasGridLayout(bool|Closure|null $condition = true): static
    {
        $this->tableHasGridLayout = $condition;

        return $this;
    }

    public function tableHasIconActions(bool|Closure|null $condition = false): static
    {
        $this->tableHasIconActions = $condition;

        return $this;
    }
}