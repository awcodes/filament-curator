<?php

namespace Awcodes\Curator\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class InstallCommand extends Command
{
    use CanManipulateFiles;

    public $signature = 'curator:install';

    public $description = 'Install Curator plugin into the app.';

    public function handle(): int
    {
        // use uuid
        $useUuid = confirm(
            label: 'Use UUID?',
            default: false,
        );

        // supports tenancy
        $tenancyName = null;
        $useTenancy = confirm(
            label: 'Use Tenancy?',
            default: false,
        );

        // ask for tenant model name
        if ($useTenancy) {
            $tenancyName = text(
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
        $this->copyStubToApp(
            'migration',
            database_path('migrations/' . date('Y_m_d_His', time()) . '_create_curator_table.php'),
            [
                'use_uuid' => $useUuid ? '$table->uuid(\'id\');' : '$table->id();',
                'tenancy_name' => $useTenancy ? str($tenancyName)->snake() : 'tenant',
            ]
        );

        // handle model stub
        if ($useUuid || $useTenancy) {
            $this->copyStubToApp(
                'media',
                app_path('Models/Media.php'),
                [
                    'imports' => $imports,
                    'traits' => $useUuid ? 'use HasUuids;' : '',
                    'tenancy' => $useTenancy ? 'public function ' . str($tenancyName)->snake() . '(): BelongsTo' . PHP_EOL . '    {' . PHP_EOL . '        return $this->belongsTo(' . $tenancyName . '::class);' . PHP_EOL . '    }' : '',
                ]
            );

            $this->callSilently("vendor:publish", [
                '--tag' => "curator-config",
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
                        "tenancy' => [\n            'enabled' => false,\n            'relationship_name' => null,\n        ]," => "tenancy' => [\n            'enabled' => true,\n            'relationship_name' => " . str($tenancyName)->snake() . ",\n        ],"
                    ]
                );
            }

            $this->replaceInFile(
                app_path('Models/Media.php'),
                [
                    "\n\n" => "\n",
                    'class Media' => "\nclass Media",
                    'namespace App\Models;' => "namespace App\Models;\n"
                ]
            );
        }

        // generate glide token
        $this->call('curator:token');

        // ask to run migrations
        if (confirm('Would you like to run the migrations now?')) {
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
