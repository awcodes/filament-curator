<?php

declare(strict_types=1);

namespace Awcodes\Curator;

use Awcodes\Curator\Resources\Media\MediaResource;
use BackedEnum;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use UnitEnum;

class CuratorPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string|Closure|null $label = null;

    protected string|UnitEnum|Closure|null $navigationGroup = null;

    protected string|BackedEnum|Closure|null $navigationIcon = null;

    protected int|Closure|null $navigationSort = null;

    protected string|Closure|null $pluralLabel = null;

    protected bool|Closure|null $shouldRegisterNavigation = null;

    protected bool|Closure|null $shouldShowBadge = null;

    protected bool|Closure|null $supportsCurations = null;

    protected bool|Closure|null $supportsFileSwap = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): Plugin
    {
        return filament(app(static::class)->getId());
    }

    public function getId(): string
    {
        return 'awcodes/curator';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                config('curator.resource.resource', MediaResource::class),
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    /**
     * Getters
     */
    public function getLabel(): string
    {
        return $this->evaluate($this->label)
            ?? config('curator.resource.label');
    }

    public function getPluralLabel(): string
    {
        return $this->evaluate($this->pluralLabel)
            ?? config('curator.resource.plural_label');
    }

    public function getNavigationGroup(): string|UnitEnum|null
    {
        return $this->evaluate($this->navigationGroup)
            ?? config('curator.resource.navigation.group');
    }

    public function getNavigationIcon(): string|BackedEnum|null
    {
        return $this->evaluate($this->navigationIcon)
            ?? config('curator.resource.navigation.icon');
    }

    public function getNavigationSort(): ?int
    {
        return $this->evaluate($this->navigationSort)
            ?? config('curator.resource.navigation.sort');
    }

    public function shouldRegisterNavigation(): bool
    {
        return $this->evaluate($this->shouldRegisterNavigation)
            ?? config('curator.resource.navigation.should_register');
    }

    public function shouldShowBadge(): bool
    {
        return $this->evaluate($this->shouldShowBadge)
            ?? config('curator.resource.navigation.should_show_badge');
    }

    public function supportsCurations(): bool
    {
        return $this->evaluate($this->supportsCurations)
            ?? config('curator.features.curations', true);
    }

    public function supportsFileSwap(): bool
    {
        return $this->evaluate($this->supportsFileSwap)
            ?? config('curator.features.file_swap', true);
    }

    /**
     * Setters
     */
    public function curations(bool|Closure $condition = true): static
    {
        $this->supportsCurations = $condition;

        return $this;
    }

    public function fileSwap(bool|Closure $condition = true): static
    {
        $this->supportsFileSwap = $condition;

        return $this;
    }

    public function label(string|Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function navigationGroup(string|UnitEnum|Closure|null $group = null): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationIcon(string|BackedEnum|Closure $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationSort(int|Closure $order): static
    {
        $this->navigationSort = $order;

        return $this;
    }

    public function pluralLabel(string|Closure $label): static
    {
        $this->pluralLabel = $label;

        return $this;
    }

    public function registerNavigation(bool|Closure $condition = true): static
    {
        $this->shouldRegisterNavigation = $condition;

        return $this;
    }

    public function showBadge(bool|Closure $condition = true): static
    {
        $this->shouldShowBadge = $condition;

        return $this;
    }
}
