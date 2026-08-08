<?php

declare(strict_types=1);

namespace Awcodes\Curator\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class InstallCommand extends Command
{
    use CanManipulateFiles;

    public $signature = 'curator:install
        {--use-uuid= : Use UUID for the primary key (default: false)}
        {--tenancy-name= : The name of the tenant model (e.g., Team)}
        {--run-migrations= : Run migrations after installation (default: false)}
    ';

    public $description = 'Install Curator plugin into the app.';

    public function handle(): int
    {
        // use uuid
        $useUuid = $this->option('use-uuid') ?? confirm(
            label: 'Use UUID?',
            default: false,
        );

        // supports tenancy
        $tenancyName = null;
        $useTenancy = $this->option('tenancy-name') ?? confirm(
            label: 'Use Tenancy?',
            default: false,
        );

        // ask for tenant model name
        if ($useTenancy) {
            $tenancyName = $this->option('tenancy-name') ?? text(
                label: 'Tenant Model?',
                placeholder: 'Team',
                required: true,
            );

            $tenancyName = str($tenancyName)->afterLast('\\')->before('::class')->trim();
        }

        $imports = collect([
            "use Illuminate\Database\Eloquent\Concerns\HasUuids;" => $useUuid,
            "use Illuminate\Database\Eloquent\Relations\BelongsTo;" => $useTenancy,
        ])->filter()->keys()->implode("\n");

        // handle migration stub

        $migrationPath = database_path('migrations/' . date('Y_m_d_His', time()) . '_create_curator_table.php');

        $this->copyStubToApp(
            'migration',
            $migrationPath,
            [
                'use_uuid' => $useUuid ? '$table->uuid(\'id\')->primary();' : '$table->id();',
                'tenancy_name' => $useTenancy ? str($tenancyName)->snake() : 'tenant',
            ]
        );

        $this->info("Curator migration [{$migrationPath}] created successfully.");

        // handle model stub
        if ($useUuid || $useTenancy) {
            $modelPath = app_path('Models/Media.php');

            $this->copyStubToApp(
                'media',
                $modelPath,
                [
                    'imports' => $imports,
                    'traits' => $useUuid ? 'use HasUuids;' : '',
                    'tenancy' => $useTenancy ? 'public function ' . str($tenancyName)->snake() . '(): BelongsTo' . PHP_EOL . '    {' . PHP_EOL . '        return $this->belongsTo(' . $tenancyName . '::class);' . PHP_EOL . '    }' : '',
                ]
            );

            $this->info("Media model [{$modelPath}] created successfully.");

            $this->callSilently('vendor:publish', [
                '--tag' => 'curator-config',
            ]);

            $this->replaceInFile(
                config_path('curator.php'),
                [
                    '\\Awcodes\\Curator\\Models\\Media::class' => '\\App\\Models\\Media::class',
                ]
            );

            if ($useTenancy) {
                $this->replaceInFile(
                    config_path('curator.php'),
                    [
                        "tenancy' => [\n            'enabled' => false,\n            'relationship_name' => null,\n        ]," => "tenancy' => [\n            'enabled' => true,\n            'relationship_name' => " . str($tenancyName)->snake() . ",\n        ],",
                    ]
                );
            }

            $this->replaceInFile(
                app_path('Models/Media.php'),
                [
                    "\n\n" => "\n",
                    'class Media' => "\nclass Media",
                    'namespace App\Models;' => "namespace App\Models;\n",
                ]
            );
        }

        // generate glide token
        $this->call('curator:token');

        // ask to run migrations
        if ($this->option('run-migrations') ?? confirm('Would you like to run the migrations now?')) {
            $this->comment('Running migrations...');

            $this->call('migrate');
        }

        $this->info('Curator has been installed successfully.');

        return self::SUCCESS;
    }

    private function replaceInFile(string $file, array $replacements): void
    {
        $contents = file_get_contents($file);

        file_put_contents(
            $file,
            str_replace(
                array_keys($replacements),
                array_values($replacements),
                $contents
            )
        );
    }
}
