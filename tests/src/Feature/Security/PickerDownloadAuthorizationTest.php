<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaManagementDeniedPolicy;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Workbench\App\Filament\Resources\Posts\Pages\EditPost;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;

function pickerMedia(array $overrides = [], ?int $tenantId = null): Media
{
    $media = makeMedia(['width' => 800, 'height' => 600, ...$overrides]);

    if ($tenantId !== null) {
        $media->forceFill(['tenant_id' => $tenantId])->save();
    }

    return $media;
}

function forgedPickerItem(array $overrides): array
{
    $item = pickerMedia(['name' => 'decoy', 'path' => 'decoy.jpg'])->toArray();

    unset($item['id']);

    return [...$item, ...$overrides];
}

function pickerDownload(array $item): mixed
{
    $post = Post::create(['title' => 'Picker download']);

    $livewire = Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->set('data.gallery', [(string) Str::uuid() => $item]);

    $uuid = array_key_first($livewire->get('data.gallery'));

    return $livewire->callAction(TestAction::make('download')->schemaComponent('gallery', schema: 'form')->arguments(['uuid' => $uuid]));
}

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');
    Storage::disk('local')->put('secrets/x.txt', 'secret-contents');
});

test('a picker item downloads its own file', function () {
    Storage::disk('public')->put('own-file.jpg', 'own-contents');

    $media = pickerMedia(['name' => 'own-file', 'path' => 'own-file.jpg']);

    pickerDownload($media->toArray())->assertFileDownloaded('own-file.jpg', 'own-contents');
});

test('a forged picker item without a media record downloads nothing', function () {
    pickerDownload(forgedPickerItem(['id' => 'missing', 'disk' => 'local', 'path' => 'secrets/x.txt']))->assertNoFileDownloaded();
});

test('a forged picker item with an unknown id downloads nothing', function () {
    pickerDownload(forgedPickerItem(['id' => 999999, 'disk' => 'local', 'path' => 'secrets/x.txt']))->assertNoFileDownloaded();
});

test('a picker item uses the record disk and path, not the state', function () {
    Storage::disk('public')->put('real-file.jpg', 'real-contents');

    $media = pickerMedia(['name' => 'real-file', 'path' => 'real-file.jpg']);

    pickerDownload([...$media->toArray(), 'disk' => 'local', 'path' => 'secrets/x.txt'])
        ->assertFileDownloaded('real-file.jpg', 'real-contents');
});

test('an unknown uuid downloads nothing', function () {
    $post = Post::create(['title' => 'Picker download']);

    Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->set('data.gallery', [])
        ->callAction(TestAction::make('download')->schemaComponent('gallery', schema: 'form')->arguments(['uuid' => 'missing']))
        ->assertNoFileDownloaded();
});

test('the picker download honours a denying view policy', function () {
    Storage::disk('public')->put('view-denied.jpg', 'denied-contents');

    Gate::policy(Media::class, MediaManagementDeniedPolicy::class);

    $media = pickerMedia(['name' => 'view-denied', 'path' => 'view-denied.jpg']);

    pickerDownload($media->toArray())->assertNoFileDownloaded();
});

describe('with tenancy', function () {
    beforeEach(function () {
        config()->set('curator.features.tenancy.enabled', true);
        config()->set('curator.features.tenancy.relationship_name', 'tenant');

        Filament::getCurrentOrDefaultPanel()->tenant(User::class);

        $this->tenant = User::factory()->create();
        $this->otherTenant = User::factory()->create();

        Filament::setTenant($this->tenant);
    });

    test('media from another tenant is refused', function () {
        Storage::disk('public')->put('other-tenant.jpg', 'other-contents');

        $media = pickerMedia(['name' => 'other-tenant', 'path' => 'other-tenant.jpg'], $this->otherTenant->id);

        pickerDownload($media->toArray())->assertNoFileDownloaded();
    });

    test('media from the current tenant downloads', function () {
        Storage::disk('public')->put('own-tenant.jpg', 'own-contents');

        $media = pickerMedia(['name' => 'own-tenant', 'path' => 'own-tenant.jpg'], $this->tenant->id);

        pickerDownload($media->toArray())->assertFileDownloaded('own-tenant.jpg', 'own-contents');
    });
});
