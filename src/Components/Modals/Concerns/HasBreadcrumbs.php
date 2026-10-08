<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals\Concerns;

use Awcodes\Curator\Support\MediaScope;
use Livewire\Attributes\Locked;

trait HasBreadcrumbs
{
    #[Locked]
    public ?array $breadcrumbs = null;

    abstract public function getMediaScope(): MediaScope;

    public function getBreadcrumbs(): void
    {
        $scope = $this->getMediaScope();

        $crumbs = array_values(array_filter(
            $this->generateBreadcrumbs($this->directory, $this->directories ?? []),
            fn (array $crumb): bool => $scope->allowsDirectory($crumb['path'] ?? null),
        ));

        // A panel limited to a directory starts there: the disk and the folders above it aren't offered.
        if ($scope->isLimitedToDirectory()) {
            $this->breadcrumbs = $crumbs;

            return;
        }

        $this->breadcrumbs = [
            [
                'label' => trans('curator::views.details.disk'),
                'name' => 'disk',
                'path' => $this->diskName,
                'parent_path' => null,
            ],
            ...$crumbs,
        ];
    }

    public function generateBreadcrumbs($currentDir, array $dirs): array
    {
        if (! $currentDir) {
            return [];
        }

        $item = is_string($currentDir) && array_key_exists($currentDir, $dirs)
        ? $dirs[$currentDir]
        : (is_string($currentDir) ? ['label' => $currentDir, 'path' => $currentDir] : $currentDir);

        $crumbs = [];
        if (isset($item['parent_path']) && $item['parent_path'] && array_key_exists($item['parent_path'], $dirs)) {
            $crumbs = $this->generateBreadcrumbs($dirs[$item['parent_path']], $dirs);
        }

        $crumbs[] = $item;

        return $crumbs;
    }
}
