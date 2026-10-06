<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

// The command writes a config file, a model, a migration and the .env. The suite runs in parallel against one
// shared Testbench skeleton, so the app's paths point at a private directory for each test instead.
beforeEach(function () {
    $this->sandbox = sys_get_temp_dir() . '/curator-install-' . Str::random(8);

    File::ensureDirectoryExists("{$this->sandbox}/config");
    File::ensureDirectoryExists("{$this->sandbox}/app/Models");
    File::ensureDirectoryExists("{$this->sandbox}/database/migrations");
    File::put("{$this->sandbox}/.env", '');

    $this->app->useConfigPath("{$this->sandbox}/config");
    $this->app->useAppPath("{$this->sandbox}/app");
    $this->app->useDatabasePath("{$this->sandbox}/database");
    $this->app->useEnvironmentPath($this->sandbox);
});

afterEach(function () {
    File::deleteDirectory($this->sandbox);
});

test('points the published config at the generated model and writes a valid tenancy relationship', function () {
    $this->artisan('curator:install', [
        '--use-uuid' => '1',
        '--tenancy-name' => 'Team',
        '--run-migrations' => '0',
    ])->assertSuccessful();

    $config = require "{$this->sandbox}/config/curator.php";

    expect($config['model'])->toBe('App\\Models\\Media')
        ->and($config['features']['tenancy']['enabled'])->toBeTrue()
        ->and($config['features']['tenancy']['relationship_name'])->toBe('team')
        ->and(File::get("{$this->sandbox}/app/Models/Media.php"))
        ->toContain('use HasUuids;')
        ->toContain('public function team(): BelongsTo')
        ->and(File::glob("{$this->sandbox}/database/migrations/*_create_curator_table.php"))->toHaveCount(1)
        ->and(File::get("{$this->sandbox}/.env"))->toContain('CURATOR_GLIDE_TOKEN=');
});

test('points the published config at the generated model without tenancy', function () {
    $this->artisan('curator:install', [
        '--use-uuid' => '1',
        '--tenancy-name' => '',
        '--run-migrations' => '0',
    ])->assertSuccessful();

    $config = require "{$this->sandbox}/config/curator.php";

    expect($config['model'])->toBe('App\\Models\\Media')
        ->and($config['features']['tenancy']['enabled'])->toBeFalse();
});

test('leaves an existing config file in place', function () {
    File::put("{$this->sandbox}/config/curator.php", "<?php\n\nreturn ['model' => Awcodes\\Curator\\Models\\Media::class, 'custom' => true];\n");

    $this->artisan('curator:install', [
        '--use-uuid' => '1',
        '--tenancy-name' => '',
        '--run-migrations' => '0',
    ])->assertSuccessful();

    $config = require "{$this->sandbox}/config/curator.php";

    expect($config['custom'])->toBeTrue()
        ->and($config['model'])->toBe('App\\Models\\Media');
});
