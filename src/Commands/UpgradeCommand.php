<?php

namespace Awcodes\Curator\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpgradeCommand extends Command
{
    public $signature = 'curator:upgrade';

    public $description = 'Upgrade Curator DB from v2 to v3';

    public function handle(): int
    {
        $this->warn('This will update Curator\'s database schema for 3.x.');

        $this->confirm('Do you wish to continue?', true);

        $this->info('Backing up table to `media_tmp`...');

        // remove exiting tmp table if exists
        if (Schema::hasTable('media_tmp')) {
            Schema::dropIfExists('media_tmp');
        }

        $tableName = app(config('curator.model'))->getTable();

        // get db driver
        $driver = Arr::get(DB::connection()->getConfig(), 'driver');

        // clone db as a backup
        match ($driver) {
            'sqlite' => function () use ($tableName): void {
                DB::statement('CREATE TABLE media_tmp AS SELECT * FROM ' . $tableName);
            },
            'pgsql' => function () use ($tableName): void {
                DB::statement('CREATE TABLE media_tmp AS (SELECT * FROM ' . $tableName . ')');
            },
            default => function () use ($tableName): void {
                DB::statement('CREATE TABLE media_tmp LIKE media');
                DB::statement('INSERT media_tmp SELECT * FROM ' . $tableName);
            }
        };

        // publish migration
        $this->info('Publishing migration...');

        $migrationsPath = realpath(__DIR__ . '/../../database/migrations');

        foreach (glob("{$migrationsPath}/upgrade_*.php.stub") as $filename) {
            File::copy(
                $filename,
                $this->generateMigrationName(
                    basename($filename),
                    Carbon::now()->addSecond()
                )
            );
        }

        $this->info('Running migration...');

        $this->call('migrate');

        // process existing entries to fill new db fields
        $this->info('Updating media entries...');

        $mediaCount = DB::table($tableName)->count();

        if ($mediaCount > 0) {
            $progress = $this->output->createProgressBar($mediaCount);

            DB::table($tableName)->chunkById(500, function ($media) use ($progress, $tableName) {
                foreach ($media as $item) {
                    DB::table($tableName)
                        ->where('id', $item->id)
                        ->update([
                            'visibility' => Storage::disk($item->disk)->getVisibility($item->path),
                        ]);
                }

                $progress->advance();
            });

            $progress->finish();
        } else {
            $this->comment('No media entries to process.');
        }

        foreach (['public_id', 'filename'] as $column) {
            if (Schema::hasColumn($tableName, $column)) {
                Schema::table($tableName, function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        $this->newLine();
        $this->info('Curator successfully upgraded.');

        return self::SUCCESS;
    }

    public static function generateMigrationName(string $migrationFileName, Carbon $now): string
    {
        $migrationsPath = 'migrations/';
        $migrationFileName = Str::of($migrationFileName)->rtrim('.stub')->toString();

        $len = strlen($migrationFileName) + 4;

        if (Str::contains($migrationFileName, '/')) {
            $migrationsPath .= Str::of($migrationFileName)->beforeLast('/')->finish('/');
            $migrationFileName = Str::of($migrationFileName)->afterLast('/');
        }

        foreach (glob(database_path("{$migrationsPath}*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName . '.php')) {
                return $filename;
            }
        }

        return database_path($migrationsPath . $now->format('Y_m_d_His') . '_' . Str::of($migrationFileName)->snake()->finish('.php'));
    }
}
