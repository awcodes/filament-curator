<?php

declare(strict_types=1);

namespace Awcodes\Curator\Components\Modals\Concerns;

use Awcodes\Curator\Support\MediaScope;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;

trait InteractsWithStorage
{
    #[Locked]
    public ?array $directories = null;

    #[Locked]
    public ?array $subDirectories = null;

    abstract public function getMediaScope(): MediaScope;

    /**
     * @throws BindingResolutionException
     */
    public function getDirectories(): void
    {
        $scope = $this->getMediaScope();

        $directories = $scope->query()
            ->whereNotNull('directory')
            ->distinct()
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

        if (! $scope->isLimitedToDirectory()) {
            return;
        }

        // A limited panel shows nothing above its directory: the folders that only lead to it are dropped, and the
        // directory itself is the top of the tree, even when it holds no media yet.
        $this->directories = array_filter(
            $this->directories,
            fn (array $directory): bool => $scope->allowsDirectory($directory['path']),
        );

        $limit = $scope->getDirectory();

        $this->directories[$limit] ??= [
            'label' => Str::of($limit)->afterLast('/')->replace('-', ' ')->title()->toString(),
            'name' => Str::of($limit)->afterLast('/')->toString(),
            'path' => $limit,
            'parent_path' => Str::contains($limit, '/') ? Str::of($limit)->beforeLast('/')->toString() : '',
        ];
    }

    public function getSubDirectories(): void
    {
        $this->subDirectories = collect($this->directories)
            ->where('parent_path', $this->directory ?? '')
            ->toArray();
    }

    public function handleDirectoryChange(string $directory): void
    {
        // Uploads are stored in the directory being browsed, so the client may only move between the disk root,
        // the configured directory and directories that already hold media the panel lists, never name a new one,
        // and never leave the directory the panel is limited to.
        $isKnownDirectory = $directory === $this->diskName
            || $directory === ($this->settings['directory'] ?? null)
            || array_key_exists($directory, $this->directories ?? []);

        $target = $directory === $this->diskName ? null : $directory;

        if (! $isKnownDirectory || ! $this->getMediaScope()->allowsDirectory($target)) {
            return;
        }

        $this->breadcrumbs = null;
        $this->directory = $target;
        $this->files = $this->getFiles();
    }
}
