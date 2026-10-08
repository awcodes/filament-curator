<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Models\Author;
use Filament\Facades\Filament;

/**
 * Media created through the picker's panel or the multi upload action is mass
 * assigned from the uploader's data, the way these tests create it.
 */
function tenantAssignmentUploaderData(): array
{
    return [
        'disk' => 'public',
        'directory' => null,
        'visibility' => 'public',
        'name' => 'upload',
        'path' => 'upload.jpg',
        'size' => 10,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ];
}

function tenantAssignmentWithTenant(): Author
{
    $tenant = Author::query()->forceCreate(['name' => 'Tenant', 'username' => uniqid('tenant')]);

    Filament::partialMock()->shouldReceive('hasTenancy')->andReturn(true);
    Filament::partialMock()->shouldReceive('getTenant')->andReturn($tenant);

    return $tenant;
}

test('uploaded media is assigned to the current tenant when tenancy is enabled', function () {
    config(['curator.features.tenancy.enabled' => true, 'curator.features.tenancy.relationship_name' => 'tenant']);
    $tenant = tenantAssignmentWithTenant();

    $media = Media::create(tenantAssignmentUploaderData());

    expect($media->refresh()->tenant_id)->toBe($tenant->getKey());
});

test('a tenant already set on the media is kept', function () {
    config(['curator.features.tenancy.enabled' => true, 'curator.features.tenancy.relationship_name' => 'tenant']);
    tenantAssignmentWithTenant();
    $other = Author::query()->forceCreate(['name' => 'Tenant', 'username' => uniqid('tenant')]);

    $media = new Media(tenantAssignmentUploaderData());
    $media->tenant_id = $other->getKey();
    $media->save();

    expect($media->refresh()->tenant_id)->toBe($other->getKey());
});

test('media is not assigned a tenant when tenancy is disabled', function () {
    config(['curator.features.tenancy.enabled' => false, 'curator.features.tenancy.relationship_name' => 'tenant']);
    tenantAssignmentWithTenant();

    $media = Media::create(tenantAssignmentUploaderData());

    expect($media->refresh()->tenant_id)->toBeNull();
});
