<?php

namespace Awcodes\Curator;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class CuratorPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string | Closure | null $label = null;

    protected string | null $navigationGroup = null;

    protected string | null $navigationIcon = null;

    protected int | null $navigationSort = null;

    protected string | Closure | null $pluralLabel = null;

    protected string | null $resource = null;

    public function getId(): string
    {
        return 'awcodes/curator';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                $this->getResource(),
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
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
        return $this->resource ?? config('curator.resources.resource');
    }

    public function getLabel(): string
    {
        return $this->evaluate($this->label) ?? config('curator.resources.label');
    }

    public function getPluralLabel(): string
    {
        return $this->evaluate($this->pluralLabel) ?? config('curator.resources.plural_label');
    }

    public function getNavigationGroup(): string | null
    {
        return $this->navigationGroup ?? config('curator.resources.navigation_group');
    }

    public function getNavigationIcon(): string | null
    {
        return $this->navigationIcon ?? config('curator.resources.navigation_icon');
    }

    public function getNavigationSort(): int | null
    {
        return $this->navigationSort ?? config('curator.resources.navigation_sort');
    }

    public function navigationGroup(string | null $group = null): static
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

    public function pluralLabel(string | Closure $label): static
    {
        $this->pluralLabel = $label;

        return $this;
    }

    public function resource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function label(string | Closure $label): static
    {
        $this->label = $label;

        return $this;
    }
}
