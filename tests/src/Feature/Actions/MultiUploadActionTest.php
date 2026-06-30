<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\ListMedia;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaBulkUploadPolicy;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaCreateAllowedPolicy;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaCreateDeniedPolicy;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

$action = 'curator_multi_upload';

test('bulk upload is visible when no media policy is registered', function () use ($action) {
    Livewire::test(ListMedia::class)
        ->assertActionVisible($action);
});

test('bulk upload falls back to the create ability when allowed', function () use ($action) {
    Gate::policy(Media::class, MediaCreateAllowedPolicy::class);

    Livewire::test(ListMedia::class)
        ->assertActionVisible($action);
});

test('bulk upload is hidden when the create ability is denied', function () use ($action) {
    Gate::policy(Media::class, MediaCreateDeniedPolicy::class);

    Livewire::test(ListMedia::class)
        ->assertActionHidden($action);
});

test('bulk upload uses the bulkUpload ability when the policy provides it', function () use ($action) {
    // create() is allowed on this policy, so the action being hidden proves the
    // dedicated bulkUpload() ability takes precedence over create().
    Gate::policy(Media::class, MediaBulkUploadPolicy::class);

    Livewire::test(ListMedia::class)
        ->assertActionHidden($action);
});
