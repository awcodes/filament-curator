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

    protected ?string $navigationGroup = null;

    protected ?string $navigationIcon = null;

    protected ?int $navigationSort = null;

    protected ?bool $navigationCountBadge = null;

    protected string | Closure | null $pluralLabel = null;

    protected ?string $resource = null;

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

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup ?? config('curator.resources.navigation_group');
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon ?? config('curator.resources.navigation_icon');
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort ?? config('curator.resources.navigation_sort');
    }

    public function getNavigationCountBadge(): ?bool
    {
        return $this->navigationCountBadge ?? config('curator.resources.navigation_count_badge');
    }

    public function navigationGroup(string $group = null): static
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

    public function navigationCountBadge(bool $show = true): static
    {
        $this->navigationCountBadge = $show;

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
