<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals\Concerns;

use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

trait InteractsWithStorage
{
    public ?array $directories = null;

    public ?array $subDirectories = null;

    /**
     * @throws BindingResolutionException
     */
    public function getDirectories(): void
    {
        $directories = App::make(Media::class)::query()->select('directory')
            ->whereNotNull('directory')
            ->distinct()
            ->get()
            ->pluck('directory')
            ->toArray();

        $this->directories = collect($directories)
            ->mapWithKeys(function ($item): array {
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

        // Synthesize all missing ancestor directories so the full path hierarchy is navigable.
        // A single foreach only visits the original entries, so we repeat until no new entries are added.
        do {
            $addedNew = false;
            foreach ($this->directories as $directory) {
                $parentPath = $directory['parent_path'];
                if (filled($parentPath) && ! array_key_exists($parentPath, $this->directories)) {
                    $name = Str::of($parentPath)->afterLast('/')->toString();
                    $this->directories[$parentPath] = [
                        'label' => Str::of($name)->replace('-', ' ')->title()->toString(),
                        'name' => $name,
                        'path' => $parentPath,
                        'parent_path' => Str::contains($parentPath, '/')
                            ? Str::of($parentPath)->beforeLast('/')->toString()
                            : '',
                    ];
                    $addedNew = true;
                }
            }
        } while ($addedNew);
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
