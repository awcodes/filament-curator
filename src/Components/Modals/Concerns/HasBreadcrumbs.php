<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals\Concerns;

trait HasBreadcrumbs
{
    public ?array $breadcrumbs = null;

    public function getBreadcrumbs(): void
    {
        $this->breadcrumbs = [
            [
                'label' => 'Disk',
                'name' => 'disk',
                'path' => $this->diskName,
                'parent_path' => null,
            ],
            ...$this->generateBreadcrumbs($this->directory, $this->directories) ?? [],
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
