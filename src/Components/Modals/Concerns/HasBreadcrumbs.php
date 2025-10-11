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

        $item = is_string($currentDir) ? $dirs[$currentDir] : $currentDir;

        if ($item['parent_path']) {
            $crumbs = $this->generateBreadcrumbs($dirs[$item['parent_path']], $dirs);
        }

        $crumbs[] = $item;

        return $crumbs;
    }
}
