<?php

declare(strict_types=1);

namespace Workbench\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AssertCuratorMigration extends Command
{
    protected $signature = 'workbench:assert-curator-migration';

    protected $description = 'Assert that exactly one Curator consumer migration is materialized';

    public function handle(): int
    {
        $migrations = File::glob(database_path('migrations/*_create_curator_table.php'));

        if (count($migrations) !== 1) {
            $this->components->error(sprintf(
                'Expected exactly one materialized Curator migration; found %d.',
                count($migrations),
            ));

            return self::FAILURE;
        }

        $this->components->info('Exactly one Curator consumer migration is materialized.');

        return self::SUCCESS;
    }
}
