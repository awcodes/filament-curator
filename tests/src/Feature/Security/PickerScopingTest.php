<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Enums\MimeType;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Support\MediaScope;
use Awcodes\Curator\Tests\Fixtures\Livewire\PickerForm;
use Awcodes\Curator\Tests\Fixtures\Models\UuidMedia;
use Filament\Facades\Filament;
use Filament\Schemas\Schema as FilamentSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Workbench\App\Filament\Resources\Posts\Pages\EditPost;
use Workbench\App\Models\Mediable;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;

/*
 * Tenancy is covered both ways an app can set it up: Curator's own tenant filter (`tenantAware()`, without the media
 * resource on the tenant panel), and Filament's tenant scope on the Media model (the resource registered on the
 * tenant panel), which is stood in for here by an equivalent global scope.
 */
dataset('tenancy', [
    'curator tenant filter' => 'curator',
    'filament tenant scope' => 'filament',
]);

const SCOPING_FILAMENT_SCOPE = 'curator-test-filament-tenancy';

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');

    PickerForm::$configurePicker = null;
    PickerForm::$fieldName = 'media';
});

afterEach(function () {
    Model::clearBootedModels();
});

/**
 * @return array{0: User, 1: User}
 */
function scopingTenancy(string $mode): array
{
    config()->set('curator.features.tenancy.enabled', $mode === 'curator');
    config()->set('curator.features.tenancy.relationship_name', 'tenant');

    Filament::getCurrentOrDefaultPanel()->tenant(User::class);

    $tenant = User::factory()->create();
    $other = User::factory()->create();

    Filament::setTenant($tenant);

    if ($mode === 'filament') {
        Media::addGlobalScope(SCOPING_FILAMENT_SCOPE, fn (Builder $query) => $query->where('tenant_id', Filament::getTenant()?->getKey()));
    }

    return [$tenant, $other];
}

function scopedMedia(string $name, array $attributes, ?Model $tenant): Media
{
    $type = $attributes['type'] ?? 'image/jpeg';
    $ext = MimeType::from($type)->getExt();
    $directory = array_key_exists('directory', $attributes) ? $attributes['directory'] : 'uploads';

    return Media::query()->forceCreate([
        'disk' => 'public',
        'visibility' => 'public',
        'name' => $name,
        'title' => "report {$name}",
        'path' => ltrim("{$directory}/{$name}.{$ext}", '/'),
        'size' => 100,
        'width' => 10,
        'height' => 10,
        'ext' => $ext,
        'tenant_id' => $tenant?->getKey(),
        ...$attributes,
        'type' => $type,
        'directory' => $directory,
    ]);
}

/**
 * @return array<string, Media>
 */
function seedScopedMedia(Model $tenant, Model $other): array
{
    return [
        'own' => scopedMedia('own', [], $tenant),
        'nested' => scopedMedia('nested', ['directory' => 'uploads/2024', 'type' => 'image/png'], $tenant),
        'svg' => scopedMedia('svg', ['type' => 'image/svg+xml'], $tenant),
        'otherDirectory' => scopedMedia('other-directory', ['directory' => 'secret'], $tenant),
        'siblingDirectory' => scopedMedia('sibling-directory', ['directory' => 'uploads-old'], $tenant),
        'root' => scopedMedia('root', ['directory' => null], $tenant),
        'otherDisk' => scopedMedia('other-disk', ['disk' => 'local'], $tenant),
        'otherDiskDirectory' => scopedMedia('other-disk-directory', ['disk' => 'local', 'directory' => 'local-only'], $tenant),
        'otherTenant' => scopedMedia('other-tenant', [], $other),
        'otherTenantDirectory' => scopedMedia('other-tenant-directory', ['directory' => 'other-tenant-only'], $other),
        'pdf' => scopedMedia('pdf', ['type' => 'application/pdf'], $tenant),
        'pdfDirectory' => scopedMedia('pdf-directory', ['type' => 'application/pdf', 'directory' => 'documents'], $tenant),
    ];
}

function pickableMedia(array $attributes): Media
{
    return makeMedia(['width' => 10, 'height' => 10, 'path' => ($attributes['name'] ?? 'file') . '.jpg', ...$attributes]);
}

function scopedPanelSettings(string $mode, array $overrides = []): array
{
    return [
        'acceptedFileTypes' => ['image/*'],
        'diskName' => 'public',
        'directory' => 'uploads',
        'visibility' => 'public',
        'isMultiple' => true,
        'isLimitedToDirectory' => false,
        'isTenantAware' => $mode === 'curator',
        'tenantOwnershipRelationshipName' => 'tenant',
        'statePath' => 'data.media',
        'rules' => [],
        ...$overrides,
    ];
}

/**
 * @param  array<int, Media>  $media
 * @return array<int, string>
 */
function scopedIds(array $media): array
{
    return array_map(fn (Media $media): string => (string) $media->getKey(), $media);
}

function listedIds(Testable $panel): array
{
    return collect($panel->get('files'))->pluck('id')->map(fn (mixed $id): string => (string) $id)->sort()->values()->all();
}

function sortedIds(array $media): array
{
    return collect(scopedIds($media))->sort()->values()->all();
}

function pickerStateIds(Testable $form): array
{
    return collect($form->get('data.media'))->pluck('id')->map(fn (mixed $id): string => (string) $id)->values()->all();
}

function insertedIds(Testable $panel): array
{
    $dispatch = collect(data_get($panel->effects, 'dispatches'))->firstWhere('name', 'insert-media');

    return collect($dispatch['params'][0]['media'] ?? [])->pluck('id')->map(fn (mixed $id): string => (string) $id)->all();
}

function scopedPicker(string $mode): Closure
{
    return fn (CuratorPicker $picker): CuratorPicker => $picker
        ->multiple()
        ->disk('public')
        ->directory('uploads')
        ->acceptedFileTypes(['image/*'])
        ->limitToDirectory()
        ->tenantAware($mode === 'curator');
}

describe('the panel', function () {
    test('a wildcard type lists only its own disk, directory and tenant', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => scopedPanelSettings($mode)]);

        expect(listedIds($panel))->toBe(sortedIds([$media['own'], $media['nested'], $media['svg']]));
    })->with('tenancy');

    test('a wildcard type lists the types the uploader would accept', function () {
        $types = [
            'image/jpeg', 'image/svg+xml', 'image/x-custom+xml', 'text/plain', 'text/csv', 'text/html', 'text/xml',
            'text/javascript', 'application/xml', 'application/pdf', 'application/xhtml+xml', 'application/json',
        ];
        $accepted = ['image/*', 'text/*', 'application/json'];

        $records = collect($types)->mapWithKeys(fn (string $type): array => [$type => Media::query()->forceCreate([
            'disk' => 'public', 'directory' => null, 'visibility' => 'public', 'name' => Str::slug($type),
            'path' => Str::slug($type), 'size' => 1, 'type' => $type, 'ext' => 'bin',
        ])]);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => ['acceptedFileTypes' => $accepted, 'directory' => null]]);

        $expected = $records->filter(fn (Media $media, string $type): bool => MimeType::isAccepted($type, $accepted))->values()->all();

        expect(listedIds($panel))->toBe(sortedIds($expected))
            ->and(MimeType::isAccepted('text/html', $accepted))->toBeFalse()
            ->and(MimeType::isAccepted('image/svg+xml', $accepted))->toBeTrue();
    });

    test('a type listed exactly is listed even when a wildcard would leave it out', function () {
        $html = pickableMedia(['name' => 'page', 'path' => 'page.html', 'type' => 'text/html', 'ext' => 'html']);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => ['acceptedFileTypes' => ['text/*', 'text/html'], 'directory' => null]]);

        expect(listedIds($panel))->toBe(sortedIds([$html]));
    });

    test('search finds only accepted types on the panel disk and tenant', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => scopedPanelSettings($mode)])
            ->set('search', 'report');

        expect(listedIds($panel))->toBe(sortedIds([
            $media['own'], $media['nested'], $media['svg'], $media['otherDirectory'], $media['siblingDirectory'], $media['root'],
        ]));
    })->with('tenancy');

    test('the directory tree holds only folders with media the panel lists', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        seedScopedMedia($tenant, $other);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => scopedPanelSettings($mode)]);

        expect(collect($panel->get('directories'))->keys()->sort()->values()->all())
            ->toBe(['secret', 'uploads', 'uploads-old', 'uploads/2024']);

        foreach (['local-only', 'other-tenant-only', 'documents'] as $directory) {
            $panel->call('handleDirectoryChange', $directory)->assertSet('directory', 'uploads');
        }
    })->with('tenancy');

    test('a limited panel cannot leave its directory', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => scopedPanelSettings($mode, ['isLimitedToDirectory' => true])]);

        expect(collect($panel->get('breadcrumbs'))->pluck('path')->all())->toBe(['uploads'])
            ->and(collect($panel->get('directories'))->keys()->sort()->values()->all())->toBe(['uploads', 'uploads/2024'])
            ->and(listedIds($panel))->toBe(sortedIds([$media['own'], $media['nested'], $media['svg']]));

        $panel->assertDontSeeHtml("handleDirectoryChange('public')")
            ->assertDontSeeText(trans('curator::views.details.disk'));

        foreach (['public', 'secret', 'uploads-old', 'documents'] as $directory) {
            $panel->call('handleDirectoryChange', $directory)->assertSet('directory', 'uploads');
        }

        $panel->call('handleDirectoryChange', 'uploads/2024')
            ->assertSet('directory', 'uploads/2024')
            ->assertSeeHtml("handleDirectoryChange('uploads')")
            ->assertDontSeeHtml("handleDirectoryChange('public')");

        expect(collect($panel->get('breadcrumbs'))->pluck('path')->all())->toBe(['uploads', 'uploads/2024'])
            ->and(listedIds($panel))->toBe(sortedIds([$media['nested']]));

        $panel->call('handleDirectoryChange', 'uploads')->assertSet('directory', 'uploads');
    })->with('tenancy');

    test('a limited panel searches only its directory', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => scopedPanelSettings($mode, ['isLimitedToDirectory' => true])])
            ->set('search', 'report');

        expect(listedIds($panel))->toBe(sortedIds([$media['own'], $media['nested'], $media['svg']]));
    })->with('tenancy');

    test('an unlimited panel still offers the disk root', function () {
        pickableMedia(['name' => 'root']);

        Livewire::test(CuratorPanel::class, ['settings' => ['directory' => 'uploads']])
            ->assertSeeHtml("handleDirectoryChange('public')")
            ->call('handleDirectoryChange', 'public')
            ->assertSet('directory', null);
    });

    test('inserting drops media the panel would not list', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => scopedPanelSettings($mode)])
            ->set('selected', [
                $media['otherTenant']->toArray(),
                $media['pdf']->toArray(),
                ['id' => $media['otherDisk']->getKey()],
                $media['own']->toArray(),
                ['id' => 999999],
                $media['otherDirectory']->toArray(),
            ])
            ->callAction('insertMedia');

        expect(insertedIds($panel))->toBe(scopedIds([$media['own'], $media['otherDirectory']]));
    })->with('tenancy');

    test('a limited panel inserts only media from its directory', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => scopedPanelSettings($mode, ['isLimitedToDirectory' => true])])
            ->set('selected', [$media['otherDirectory']->toArray(), $media['siblingDirectory']->toArray(), $media['nested']->toArray()])
            ->callAction('insertMedia');

        expect(insertedIds($panel))->toBe(scopedIds([$media['nested']]));
    })->with('tenancy');

    test('the rich editor panel inserts only media from its disk', function () {
        $own = pickableMedia(['name' => 'own']);
        $otherDisk = pickableMedia(['name' => 'elsewhere', 'disk' => 'local']);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => [
            'acceptedFileTypes' => MimeType::defaults(),
            'diskName' => 'public',
            'directory' => null,
            'isMultiple' => false,
            'statePath' => 'data.content',
            'context' => 'richEditor',
        ]])
            ->set('selected', [$otherDisk->toArray(), $own->toArray()])
            ->callAction('insertMedia');

        expect(insertedIds($panel))->toBe(scopedIds([$own]));
    });

    test('an item the panel would not list cannot be downloaded from it', function () {
        Storage::disk('local')->put('uploads/other-disk.jpg', 'other-contents');

        $media = pickableMedia(['name' => 'other-disk', 'disk' => 'local', 'directory' => 'uploads', 'path' => 'uploads/other-disk.jpg']);

        Livewire::test(CuratorPanel::class, ['settings' => scopedPanelSettings('none', ['isTenantAware' => false])])
            ->callAction('downloadItem', arguments: ['item' => ['id' => $media->getKey()]])
            ->assertNoFileDownloaded();
    });
});

describe('the picker', function () {
    test('a saved value only loads media within the field scope, in order', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        PickerForm::$configurePicker = scopedPicker($mode);

        $form = Livewire::test(PickerForm::class, ['initial' => [
            $media['otherTenant']->getKey(),
            $media['nested']->getKey(),
            $media['pdf']->getKey(),
            $media['otherDisk']->getKey(),
            $media['otherDirectory']->getKey(),
            999999,
            $media['own']->getKey(),
        ]]);

        expect(pickerStateIds($form))->toBe(scopedIds([$media['nested'], $media['own']]));
    })->with('tenancy');

    test('a saved value from another tenant does not load', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        PickerForm::$configurePicker = fn (CuratorPicker $picker): CuratorPicker => $picker->tenantAware($mode === 'curator');

        $form = Livewire::test(PickerForm::class, ['initial' => $media['otherTenant']->getKey()]);

        expect($form->get('data.media'))->toBe([]);
    })->with('tenancy');

    test('media sent back from the panel is loaded again within the field scope', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        PickerForm::$configurePicker = scopedPicker($mode);

        $form = Livewire::test(PickerForm::class);

        $form->call('callSchemaComponentMethod', $form->instance()->getPickerKey(), 'updateState', [[
            'statePath' => 'data.media',
            'media' => [
                $media['otherTenant']->toArray(),
                ['id' => $media['own']->getKey(), 'disk' => 'local', 'path' => 'secrets/x.txt'],
                $media['pdf']->toArray(),
                ['id' => 999999],
                $media['otherDirectory']->toArray(),
                $media['otherDisk']->toArray(),
            ],
        ]]);

        $state = array_values($form->get('data.media'));

        expect(pickerStateIds($form))->toBe(scopedIds([$media['own']]))
            ->and($state[0]['disk'])->toBe('public')
            ->and($state[0]['path'])->toBe('uploads/own.jpg');
    })->with('tenancy');

    test('saving a selection with media outside the field scope fails', function (string $mode, string $forged) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        PickerForm::$configurePicker = scopedPicker($mode);

        $forgedItem = $forged === 'missing' ? [...$media['own']->toArray(), 'id' => 999999] : $media[$forged]->toArray();

        $form = Livewire::test(PickerForm::class)
            ->set('data.media', [
                (string) Str::uuid() => $media['own']->toArray(),
                (string) Str::uuid() => $forgedItem,
            ])
            ->call('save')
            ->assertHasErrors(['data.media']);

        expect($form->get('saved'))->toBeNull();
    })->with('tenancy')->with([
        'another tenant' => 'otherTenant',
        'a type the field does not accept' => 'pdf',
        'another disk' => 'otherDisk',
        'outside the limited directory' => 'otherDirectory',
        'a missing record' => 'missing',
    ]);

    test('saving a selection within the field scope keeps its order', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        PickerForm::$configurePicker = scopedPicker($mode);

        $form = Livewire::test(PickerForm::class)
            ->set('data.media', [
                (string) Str::uuid() => $media['nested']->toArray(),
                (string) Str::uuid() => $media['own']->toArray(),
            ])
            ->call('save')
            ->assertHasNoErrors();

        expect(array_map('strval', $form->get('saved')))->toBe(scopedIds([$media['nested'], $media['own']]));
    })->with('tenancy');

    test('a single picker saves its id', function () {
        $own = pickableMedia(['name' => 'own']);

        $form = Livewire::test(PickerForm::class, ['initial' => $own->getKey()])
            ->call('save')
            ->assertHasNoErrors();

        expect($form->get('saved'))->toBe($own->getKey());
    });

    test('the dehydrated value leaves out media outside the field scope', function () {
        $own = pickableMedia(['name' => 'own']);
        $otherDisk = pickableMedia(['name' => 'elsewhere', 'disk' => 'local']);

        $picker = CuratorPicker::make('media')->multiple()->container(FilamentSchema::make());
        $dehydrate = (new ReflectionProperty($picker, 'dehydrateStateUsing'))->getValue($picker);

        expect($dehydrate($picker, [Str::uuid()->toString() => $otherDisk->toArray(), Str::uuid()->toString() => $own->toArray()]))
            ->toBe([$own->getKey()]);
    });
});

describe('relationships', function () {
    test('a belongs-to picker saves media within its scope', function () {
        $post = Post::create(['title' => 'Post']);
        $own = pickableMedia(['name' => 'own']);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->set('data.featured_image_id', [(string) Str::uuid() => $own->toArray()])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($post->refresh()->featured_image_id)->toBe($own->getKey());

        $state = Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])->get('data.featured_image_id');

        expect(collect($state)->pluck('id')->all())->toBe([$own->getKey()]);
    });

    test('a belongs-to picker refuses media from another tenant', function () {
        [, $other] = scopingTenancy('curator');

        $post = Post::create(['title' => 'Post']);
        $foreign = scopedMedia('foreign', ['directory' => null], $other);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->set('data.featured_image_id', [(string) Str::uuid() => $foreign->toArray()])
            ->call('save')
            ->assertHasFormErrors(['featured_image_id']);

        expect($post->refresh()->featured_image_id)->toBeNull();
    });

    test('a belongs-to value saved for another tenant does not load', function () {
        [, $other] = scopingTenancy('curator');

        $foreign = scopedMedia('foreign', ['directory' => null], $other);
        $post = Post::create(['title' => 'Post', 'featured_image_id' => $foreign->getKey()]);

        $state = Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])->get('data.featured_image_id');

        expect($state)->toBe([]);
    });

    test('a morph-many picker refuses media from another disk and saves nothing', function () {
        $post = Post::create(['title' => 'Post']);
        $own = pickableMedia(['name' => 'own']);
        $otherDisk = pickableMedia(['name' => 'elsewhere', 'disk' => 'local']);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->set('data.gallery', [
                (string) Str::uuid() => $own->toArray(),
                (string) Str::uuid() => $otherDisk->toArray(),
            ])
            ->call('save')
            ->assertHasFormErrors(['gallery']);

        expect(Mediable::query()->count())->toBe(0);
    });

    test('a morph-many picker saves and loads media within its scope in order', function () {
        $post = Post::create(['title' => 'Post']);
        $first = pickableMedia(['name' => 'first']);
        $second = pickableMedia(['name' => 'second']);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->set('data.gallery', [
                (string) Str::uuid() => $second->toArray(),
                (string) Str::uuid() => $first->toArray(),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        expect(Mediable::query()->orderBy('order')->pluck('media_id')->all())->toBe([$second->getKey(), $first->getKey()]);

        $state = Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])->get('data.gallery');

        expect(collect($state)->pluck('id')->values()->all())->toBe([$second->getKey(), $first->getKey()]);
    });

    test('a morph-to-many picker syncs media within its scope', function () {
        $post = Post::create(['title' => 'Post']);
        $first = pickableMedia(['name' => 'first']);
        $second = pickableMedia(['name' => 'second']);

        PickerForm::$configurePicker = fn (CuratorPicker $picker): CuratorPicker => $picker
            ->relationship('galleryMedia', 'name')
            ->multiple();

        Livewire::test(PickerForm::class, ['record' => $post])
            ->set('data.media', [
                (string) Str::uuid() => $first->toArray(),
                (string) Str::uuid() => $second->toArray(),
            ])
            ->call('save')
            ->assertHasNoErrors();

        expect($post->galleryMedia()->pluck('curator.id')->sort()->values()->all())->toBe([$first->getKey(), $second->getKey()]);

        $form = Livewire::test(PickerForm::class, ['record' => $post->refresh()]);

        expect(collect($form->get('data.media'))->pluck('id')->sort()->values()->all())->toBe([$first->getKey(), $second->getKey()]);
    });

    test('a morph-to-many picker refuses media from another tenant', function () {
        [, $other] = scopingTenancy('curator');

        $post = Post::create(['title' => 'Post']);
        $foreign = scopedMedia('foreign', ['directory' => null], $other);

        PickerForm::$configurePicker = fn (CuratorPicker $picker): CuratorPicker => $picker
            ->relationship('galleryMedia', 'name')
            ->multiple();

        Livewire::test(PickerForm::class, ['record' => $post])
            ->set('data.media', [(string) Str::uuid() => $foreign->toArray()])
            ->call('save')
            ->assertHasErrors(['data.media']);

        expect($post->galleryMedia()->count())->toBe(0);
    });
});

describe('get_media_items', function () {
    test('without a scope it loads any id, as before', function () {
        $own = pickableMedia(['name' => 'own']);
        $otherDisk = pickableMedia(['name' => 'elsewhere', 'disk' => 'local']);

        expect(collect(get_media_items([$otherDisk->id, $own->id]))->pluck('id')->values()->all())->toBe([$otherDisk->id, $own->id]);
    });

    test('with a scope it loads only ids within it, in order, even from records passed in', function () {
        $own = pickableMedia(['name' => 'own']);
        $second = pickableMedia(['name' => 'second']);
        $otherDisk = pickableMedia(['name' => 'elsewhere', 'disk' => 'local']);

        $scope = new MediaScope(disk: 'public');

        expect(get_media_items([$second->id, $otherDisk->id, $own->id], $scope)->pluck('id')->all())->toBe([$second->id, $own->id])
            ->and(get_media_items([$otherDisk->toArray()], $scope)->all())->toBe([])
            ->and(get_media_items($own, $scope)->pluck('id')->all())->toBe([$own->id]);
    });
});

function uuidMedia(string $name, string $disk = 'public'): UuidMedia
{
    return UuidMedia::query()->create([
        'disk' => $disk, 'directory' => null, 'visibility' => 'public', 'name' => $name, 'path' => "{$name}.jpg",
        'width' => 10, 'height' => 10, 'size' => 100, 'type' => 'image/jpeg', 'ext' => 'jpg',
    ]);
}

describe('uuid keys', function () {
    beforeEach(function () {
        Schema::create('uuid_curator', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('disk');
            $table->string('directory')->nullable();
            $table->string('visibility')->default('public');
            $table->string('name');
            $table->string('path');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->string('type');
            $table->string('ext');
            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('caption')->nullable();
            $table->text('pretty_name')->nullable();
            $table->text('exif')->nullable();
            $table->longText('curations')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });

        app()->bind(Media::class, UuidMedia::class);
    });

    test('the panel lists and inserts uuid keyed media within its scope', function () {
        $own = uuidMedia('own');
        $second = uuidMedia('second');
        $otherDisk = uuidMedia('elsewhere', 'local');

        $panel = Livewire::test(CuratorPanel::class, ['settings' => ['acceptedFileTypes' => ['image/*'], 'directory' => null, 'isMultiple' => true]]);

        expect(listedIds($panel))->toBe(sortedIds([$own, $second]));

        $panel->set('selected', [$second->toArray(), $otherDisk->toArray(), $own->toArray()])->callAction('insertMedia');

        expect(insertedIds($panel))->toBe(scopedIds([$second, $own]));
    });

    test('the picker loads and saves uuid keyed media within its scope', function () {
        $own = uuidMedia('own');
        $second = uuidMedia('second');
        $otherDisk = uuidMedia('elsewhere', 'local');

        PickerForm::$configurePicker = fn (CuratorPicker $picker): CuratorPicker => $picker->multiple();

        $form = Livewire::test(PickerForm::class, ['initial' => [$second->getKey(), $otherDisk->getKey(), $own->getKey()]]);

        expect(pickerStateIds($form))->toBe(scopedIds([$second, $own]));

        $form->call('save')->assertHasNoErrors();

        expect($form->get('saved'))->toBe([$second->getKey(), $own->getKey()]);

        $form->set('data.media', [(string) Str::uuid() => $otherDisk->toArray()])
            ->call('save')
            ->assertHasErrors(['data.media']);
    });
});

/**
 * A picker bound to the post's featured image or gallery, with the scoped picker's settings.
 */
function persistedPicker(string $mode, string $relationship): void
{
    PickerForm::$fieldName = $relationship === 'featuredImage' ? 'featured_image_id' : 'gallery';

    PickerForm::$configurePicker = fn (CuratorPicker $picker): CuratorPicker => scopedPicker($mode)($picker)
        ->multiple($relationship === 'gallery')
        ->relationship($relationship, 'name')
        ->orderColumn('order');
}

function persistedGallery(Post $post, array $media): void
{
    foreach (array_values($media) as $index => $item) {
        Mediable::query()->create([
            'mediable_type' => Post::class,
            'mediable_id' => $post->getKey(),
            'media_id' => $item->getKey(),
            'order' => $index + 1,
        ]);
    }
}

function galleryIds(Post $post): array
{
    return Mediable::query()->where('mediable_id', $post->getKey())->orderBy('order')->pluck('media_id')
        ->map(fn (mixed $id): string => (string) $id)->all();
}

describe('media saved on the record', function () {
    test('a saved featured image outside the field settings keeps loading and survives an unrelated save', function (string $mode, string $kind) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);
        persistedPicker($mode, 'featuredImage');

        $post = Post::create(['title' => 'Post', 'featured_image_id' => $media[$kind]->getKey()]);

        $form = Livewire::test(PickerForm::class, ['record' => $post]);

        expect(collect($form->get('data.featured_image_id'))->pluck('id')->all())->toBe([$media[$kind]->getKey()]);

        $form->call('save')->assertHasNoErrors();

        expect($post->refresh()->featured_image_id)->toBe($media[$kind]->getKey());
    })->with('tenancy')->with([
        'a type the field no longer accepts' => 'pdf',
        'another disk' => 'otherDisk',
        'outside a later directory limit' => 'otherDirectory',
    ]);

    test('a saved gallery outside the field settings keeps loading, in order, and survives an unrelated save', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);
        persistedPicker($mode, 'gallery');

        $post = Post::create(['title' => 'Post']);
        $saved = [$media['pdf'], $media['own'], $media['otherDisk'], $media['otherDirectory']];
        persistedGallery($post, $saved);

        $form = Livewire::test(PickerForm::class, ['record' => $post]);

        expect(pickerStateIdsAt($form, 'gallery'))->toBe(scopedIds($saved));

        $form->call('save')->assertHasNoErrors();

        expect(galleryIds($post))->toBe(scopedIds($saved));
    })->with('tenancy');

    test('a saved value of a plain column outside the field settings keeps loading and saving', function () {
        $pdf = pickableMedia(['name' => 'saved', 'path' => 'saved.pdf', 'type' => 'application/pdf', 'ext' => 'pdf']);
        $post = Post::create(['title' => 'Post', 'featured_image_id' => $pdf->getKey()]);

        PickerForm::$fieldName = 'featured_image_id';
        PickerForm::$configurePicker = fn (CuratorPicker $picker): CuratorPicker => $picker->acceptedFileTypes(['image/*']);

        $form = Livewire::test(PickerForm::class, ['record' => $post]);

        expect(collect($form->get('data.featured_image_id'))->pluck('id')->all())->toBe([$pdf->getKey()]);

        $form->call('save')->assertHasNoErrors();

        expect($form->get('saved'))->toBe($pdf->getKey())
            ->and($post->refresh()->featured_image_id)->toBe($pdf->getKey());
    });

    test('a saved id of another tenant or of deleted media does not load', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);
        persistedPicker($mode, 'gallery');

        $post = Post::create(['title' => 'Post']);
        $deleted = scopedMedia('deleted', [], $tenant);
        persistedGallery($post, [$media['otherTenant'], $media['pdf'], $deleted, $media['own']]);
        $deleted->deleteQuietly();

        $form = Livewire::test(PickerForm::class, ['record' => $post]);

        expect(pickerStateIdsAt($form, 'gallery'))->toBe(scopedIds([$media['pdf'], $media['own']]));

        $featured = Post::create(['title' => 'Featured', 'featured_image_id' => $media['otherTenant']->getKey()]);
        persistedPicker($mode, 'featuredImage');

        expect(Livewire::test(PickerForm::class, ['record' => $featured])->get('data.featured_image_id'))->toBe([]);
    })->with('tenancy');

    test('a saved id of another tenant is refused when sent back', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);
        persistedPicker($mode, 'gallery');

        $post = Post::create(['title' => 'Post']);
        persistedGallery($post, [$media['otherTenant'], $media['own']]);

        Livewire::test(PickerForm::class, ['record' => $post])
            ->set('data.gallery', [
                (string) Str::uuid() => $media['otherTenant']->toArray(),
                (string) Str::uuid() => $media['own']->toArray(),
            ])
            ->call('save')
            ->assertHasErrors(['data.gallery']);
    })->with('tenancy');

    test('new media outside the field settings is still refused next to saved media', function (string $mode, string $kind) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);
        persistedPicker($mode, 'gallery');

        $post = Post::create(['title' => 'Post']);
        persistedGallery($post, [$media['pdf'], $media['own']]);

        Livewire::test(PickerForm::class, ['record' => $post])
            ->set('data.gallery', [
                (string) Str::uuid() => $media['pdf']->toArray(),
                (string) Str::uuid() => $media['own']->toArray(),
                (string) Str::uuid() => $media[$kind]->toArray(),
            ])
            ->call('save')
            ->assertHasErrors(['data.gallery']);

        expect(galleryIds($post))->toBe(scopedIds([$media['pdf'], $media['own']]));
    })->with('tenancy')->with([
        'a type the field does not accept' => 'pdfDirectory',
        'another disk' => 'otherDisk',
        'outside the limited directory' => 'otherDirectory',
        'another tenant' => 'otherTenant',
    ]);

    test('saved media can still be removed', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);
        persistedPicker($mode, 'gallery');

        $post = Post::create(['title' => 'Post']);
        persistedGallery($post, [$media['pdf'], $media['own'], $media['otherDisk']]);

        $form = Livewire::test(PickerForm::class, ['record' => $post]);

        $state = collect($form->get('data.gallery'))->reject(fn (array $item): bool => (string) $item['id'] === (string) $media['pdf']->getKey())->all();

        $form->set('data.gallery', $state)->call('save')->assertHasNoErrors();

        expect(galleryIds($post))->toBe(scopedIds([$media['own'], $media['otherDisk']]));
    })->with('tenancy');

    test('saved media sent back from the panel keeps its place', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);
        persistedPicker($mode, 'gallery');

        $post = Post::create(['title' => 'Post']);
        persistedGallery($post, [$media['pdf'], $media['own']]);

        $form = Livewire::test(PickerForm::class, ['record' => $post]);

        $form->call('callSchemaComponentMethod', $form->instance()->getPickerKey(), 'updateState', [[
            'statePath' => 'data.gallery',
            'media' => [['id' => $media['pdf']->getKey()], $media['nested']->toArray(), $media['pdfDirectory']->toArray(), $media['own']->toArray()],
        ]]);

        expect(pickerStateIdsAt($form, 'gallery'))->toBe(scopedIds([$media['pdf'], $media['nested'], $media['own']]));
    })->with('tenancy');

    test('the panel passes back the items the picker held by id, and drops other media it would not list', function (string $mode) {
        [$tenant, $other] = scopingTenancy($mode);
        $media = seedScopedMedia($tenant, $other);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => scopedPanelSettings($mode, [
            'selected' => [(string) Str::uuid() => $media['pdf']->toArray(), (string) Str::uuid() => $media['own']->toArray()],
        ])])
            ->set('selected', [$media['pdf']->toArray(), $media['pdfDirectory']->toArray(), $media['own']->toArray(), $media['nested']->toArray()])
            ->callAction('insertMedia');

        $dispatch = collect(data_get($panel->effects, 'dispatches'))->firstWhere('name', 'insert-media');
        $sent = $dispatch['params'][0]['media'];

        expect(insertedIds($panel))->toBe(scopedIds([$media['pdf'], $media['own'], $media['nested']]))
            ->and($sent[0])->toBe(['id' => (string) $media['pdf']->getKey()])
            ->and($sent[1]['path'])->toBe('uploads/own.jpg');
    })->with('tenancy');

    test('the panel sends nothing about media the picker did not hold', function () {
        $pdf = pickableMedia(['name' => 'saved', 'path' => 'saved.pdf', 'type' => 'application/pdf', 'ext' => 'pdf']);

        $panel = Livewire::test(CuratorPanel::class, ['settings' => ['acceptedFileTypes' => ['image/*'], 'directory' => null, 'isMultiple' => true]])
            ->set('selected', [$pdf->toArray()])
            ->callAction('insertMedia');

        expect(insertedIds($panel))->toBe([]);
    });
});

function pickerStateIdsAt(Testable $form, string $field): array
{
    return collect($form->get("data.{$field}"))->pluck('id')->map(fn (mixed $id): string => (string) $id)->values()->all();
}
