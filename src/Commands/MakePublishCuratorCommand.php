<?php

namespace FilamentCurator\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakePublishCuratorCommand extends Command
{
    use Concerns\CanManipulateFiles;

    public $signature = 'curator:publish';

    public $description = 'Publish Filament Curator\'s Resource.';

    public function handle(): int
    {
        $baseResourcePath = app_path((string) Str::of('Filament\\Resources\\Curator')->replace('\\', '/'),);
        $roleResourcePath = app_path((string) Str::of('Filament\\Resources\\Curator\\MediaResource.php')->replace('\\', '/'),);

        if ($this->checkForCollision([$roleResourcePath])) {
            $confirmed = $this->confirm('Curator Resource already exists. Overwrite?', true);
            if (!$confirmed) {
                return self::INVALID;
            }
        }

        (new Filesystem())->ensureDirectoryExists($baseResourcePath);
        (new Filesystem())->copyDirectory(__DIR__ . '/../../stubs/resources', $baseResourcePath);

        $this->info('Curator\'s Resource has been published successfully!');

        return self::SUCCESS;
    }
}
