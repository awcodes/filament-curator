<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

// The command writes into the Testbench application, so everything it touches is restored afterwards.
// The Glide token step writes to .env, which a fresh Testbench skeleton doesn't have.
beforeEach(function () {
    $this->envBackup = File::exists(app()->environmentFilePath()) ? File::get(app()->environmentFilePath()) : null;
    $this->migrationsBefore = File::glob(database_path('migrations/*_create_curator_table.php'));

    if ($this->envBackup === null) {
        File::put(app()->environmentFilePath(), '');
    }
});

afterEach(function () {
    File::delete(config_path('curator.php'));
    File::delete(app_path('Models/Media.php'));
    File::delete(array_diff(File::glob(database_path('migrations/*_create_curator_table.php')), $this->migrationsBefore));

    if ($this->envBackup === null) {
        File::delete(app()->environmentFilePath());
    } else {
        File::put(app()->environmentFilePath(), $this->envBackup);
    }
});

test('points the published config at the generated model and writes a valid tenancy relationship', function () {
    $this->artisan('curator:install', [
        '--use-uuid' => '1',
        '--tenancy-name' => 'Team',
        '--run-migrations' => '0',
    ])->assertSuccessful();

    $config = require config_path('curator.php');

    expect($config['model'])->toBe('App\\Models\\Media')
        ->and($config['features']['tenancy']['enabled'])->toBeTrue()
        ->and($config['features']['tenancy']['relationship_name'])->toBe('team')
        ->and(File::get(app_path('Models/Media.php')))
        ->toContain('use HasUuids;')
        ->toContain('public function team(): BelongsTo');
});

test('points the published config at the generated model without tenancy', function () {
    $this->artisan('curator:install', [
        '--use-uuid' => '1',
        '--tenancy-name' => '',
        '--run-migrations' => '0',
    ])->assertSuccessful();

    $config = require config_path('curator.php');

    expect($config['model'])->toBe('App\\Models\\Media')
        ->and($config['features']['tenancy']['enabled'])->toBeFalse();
});
