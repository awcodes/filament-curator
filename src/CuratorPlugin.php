<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Resources\MediaResource;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Facades\Blade;

class CuratorPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string | Closure | null $label = null;
    protected string | Closure | null $navigationGroup = null;
    protected ?string $navigationIcon = null;
    protected ?int $navigationSort = null;
    protected string | Closure | null $pluralLabel = null;
    protected bool | Closure | null $shouldRegisterNavigation = null;
    protected bool | Closure | null $shouldShowBadge = null;

    public function getId(): string
    {
        return 'awcodes/curator';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                MediaResource::class,
            ]);

        if (! $panel->hasPlugin('pouncePlugin')) {
            $panel->renderHook(
                name: 'panels::body.end',
                hook: fn (): string => Blade::render("@livewire('pounce')")
            );
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): Plugin
    {
        return filament(app(static::class)->getId());
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

    public function getNavigationGroup(): ?string
    {
        return $this->evaluate($this->navigationGroup)
            ?? config('curator.resource.navigation.group');
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon
            ?? config('curator.resource.navigation.icon');
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort
            ?? config('curator.resource.navigation.sort');
    }

    public function shouldRegisterNavigation(): ?bool
    {
        return $this->evaluate($this->shouldRegisterNavigation)
            ?? config('curator.resource.navigation.should_register');
    }

    public function shouldShowBadge(): ?bool
    {
        return $this->evaluate($this->shouldShowBadge)
            ?? config('curator.resource.navigation.should_show_badge');
    }

    /**
     * Setters
     */

    public function label(string | Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function navigationGroup(string | Closure $group = null): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationIcon(string | Closure $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationSort(int | Closure $order): static
    {
        $this->navigationSort = $order;

        return $this;
    }

    public function pluralLabel(string | Closure $label): static
    {
        $this->pluralLabel = $label;

        return $this;
    }

    public function registerNavigation(bool | Closure $condition = true): static
    {
        $this->shouldRegisterNavigation = $condition;

        return $this;
    }

    public function showBadge(bool | Closure $condition = true): static
    {
        $this->shouldShowBadge = $condition;

        return $this;
    }
}
