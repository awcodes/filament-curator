<?php

namespace FilamentCurator\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeInstallCuratorCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanBackupAFile;

    public $signature = 'curator:install {--F|fresh}';

    public $description = "One Command to Rule them All 🔥";

    public function handle(): int
    {
        $this->alert('The Following operations will be performed:');
        $this->info('- Publish core package config');
        $this->info('- Publish core package migrations');
        $this->warn('  - On fresh applications database will be migrated');
        $this->warn('  - You can also force this behavior by supplying the --fresh option');
        $this->info('- Publishes Resources & Pages');

        $confirmed = $this->confirm('Do you wish to continue?', true);

        if ($this->CheckIfAlreadyInstalled() && !$this->option('fresh')) {
            $this->comment('Seems you have already installed the Core package!');
            $this->comment('You should run `curator:install --fresh` instead to refresh the Core package tables and setup.');

            if ($this->confirm('Run `curator:install --fresh` instead?', false)) {
                $this->install(true);
            }

            return self::INVALID;
        }

        if ($confirmed) {
            $this->install($this->option('fresh'));
        } else {
            $this->comment('`curator:install` command was cancelled.');
        }

        return self::SUCCESS;
    }

    protected function CheckIfAlreadyInstalled(): bool
    {
        $count = $this->getTables()
            ->filter(function ($table) {
                return Schema::hasTable($table);
            })
            ->count();
        if ($count !== 0) {
            return true;
        }

        return false;
    }

    protected function getTables(): Collection
    {
        return collect(['media']);
    }

    protected function install(bool $fresh = false)
    {
        $this->call('vendor:publish', [
            '--tag' => 'filament-curator-config',
            '--tag' => 'filament-curator-migrations',
        ]);

        $this->info('Core Package config published.');

        if ($fresh) {
            try {
                Schema::disableForeignKeyConstraints();
                DB::table('migrations')->where('migration', 'like', '%_create_media_table')->delete();
                $this->getTables()->each(fn ($table) => DB::statement('DROP TABLE IF EXISTS ' . $table));
                Schema::enableForeignKeyConstraints();
            } catch (\Throwable $e) {
                $this->info($e);
            }

            $this->call('migrate');
            $this->info('Database migrations freshed up.');

            (new Filesystem())->ensureDirectoryExists(config_path());

            if ($this->isBackupPossible(config_path('filament-curator.php'), config_path('filament-curator.php.bak'))) {
                $this->info('Config backup created.');
            }

            (new Filesystem())->copy(__DIR__ . '/../../config/filament-curator.php', config_path('filament-curator.php'));
        } else {
            $this->call('migrate');
            $this->info('Database migrated.');
        }

        $this->info('Filament Curator is now installed.');
    }
}
