<?php

declare(strict_types=1);

namespace Awcodes\Curator\Config\Concerns;

use Closure;

trait SupportsTenancy
{
    protected ?bool $isTenantAware = null;

    protected string | Closure | null $tenantName = null;

    public function tenant(string | Closure $tenantName): static
    {
        $this->isTenantAware = true;
        $this->tenantName = $tenantName;

        return $this;
    }

    public function isTenantAware(): bool
    {
        return $this->isTenantAware ?? config('curator.features.tenancy.enabled');
    }

    public function getTenantName(): ?string
    {
        if (! $this->isTenantAware) {
            return null;
        }

        return $this->evaluate($this->tenantName) ?? config('curator.features.tenancy.relationship_name');
    }
}
