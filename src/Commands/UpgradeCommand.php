<?php

namespace Awcodes\Curator\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class UpgradeCommand extends Command
{
    public $signature = 'curator:upgrade';

    public $description = 'Upgrade Curator DB from v1 to v2';

    public function handle(): int
    {
        $this->warn('This will update Curator\'s database schema for 2.x.');

        $this->confirm('Do you wish to continue?', true);

        $this->info('Backing up table to `media_tmp`...');

        // remove exiting tmp table if exists
        if (Schema::hasTable('media_tmp')) {
            Schema::dropIfExists('media_tmp');
        }

        // get db driver
        $driver = Arr::get(DB::connection()->getConfig(), 'driver');

        // clone db as a backup
        match($driver) {
            'sqlite' => function(): void {
                DB::statement('CREATE TABLE media_tmp AS SELECT * FROM media');
            },
            'pgsql' => function(): void {
                DB::statement('CREATE TABLE media_tmp AS (SELECT * FROM media)');
            },
            default => function(): void {
                DB::statement('CREATE TABLE media_tmp LIKE media');
                DB::statement('INSERT media_tmp SELECT * FROM media');
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

        $mediaCount = DB::table('media')->count();

        if ($mediaCount > 0) {
            $progress = $this->output->createProgressBar($mediaCount);

            DB::table('media')->chunkById(500, function($media) use ($progress) {
                foreach ($media as $item) {
                    DB::table('media')
                        ->where('id', $item->id)
                        ->update([
                            'name' => Str::of($item->filename)->replace($item->directory . '/', '')->beforeLast('.'),
                            'path' => $item->filename
                        ]);
                }

                $progress->advance();
            });

            $progress->finish();
        } else {
            $this->comment('No media entries to process.');
        }

        foreach(['public_id', 'filename'] as $column) {
            if (Schema::hasColumn('media', $column)) {
                Schema::table('media', function (Blueprint $table) use($column) {
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
