<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals\Concerns;

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Str;

trait InteractsWithStorage
{
    public ?array $directories = null;

    public ?array $subDirectories = null;

    public function getDirectories(): void
    {
        $directories = Media::query()->select('directory')
            ->whereNotNull('directory')
            ->distinct()
            ->get()
            ->pluck('directory')
            ->toArray();

        $this->directories = collect($directories)
            ->mapWithKeys(function ($item) {
                $itemArray = explode('/', $item);
                $name = array_pop($itemArray);

                return [
                    $item => [
                        'label' => Str::of($name)
                            ->replace('-', ' ')
                            ->title()
                            ->toString(),
                        'name' => $name,
                        'path' => $item,
                        'parent_path' => implode('/', $itemArray),
                    ],
                ];
            })
            ->toArray();
    }

    public function getSubDirectories(): void
    {
        $this->subDirectories = collect($this->directories)
            ->where('parent_path', $this->directory ?? '')
            ->toArray();
    }

    public function handleDirectoryChange(string $directory): void
    {
        $this->breadcrumbs = null;
        $this->directory = $directory === $this->diskName ? null : $directory;
        $this->files = $this->getFiles();
    }
}
