<?php

namespace Awcodes\Curator\Components\Modals\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait InteractsWithStorage
{
    public ?array $directories = null;

    public ?array $subDirectories = null;

    public function getDirectories(): void
    {
        $directories = Storage::disk($this->diskName)->allDirectories();

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
