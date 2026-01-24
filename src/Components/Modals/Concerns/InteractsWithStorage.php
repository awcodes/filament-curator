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

        // Handle empty parent directories
        foreach ($this->directories as $directory) {
            if (filled($directory['parent_path']) && ! array_key_exists($directory['parent_path'], $this->directories)) {
                $this->directories[$directory['parent_path']] = [
                    'label' => Str::of($directory['parent_path'])
                        ->afterLast('/')
                        ->replace('-', ' ')
                        ->title()
                        ->toString(),
                    'name' => Str::of($directory['parent_path'])
                        ->afterLast('/')
                        ->toString(),
                    'path' => $directory['parent_path'],
                    'parent_path' => Str::of($directory['parent_path'])
                        ->contains('/')
                        ? Str::of($directory['parent_path'])
                            ->beforeLast('/')
                            ->toString()
                        : '',
                ];
            }
        }
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
