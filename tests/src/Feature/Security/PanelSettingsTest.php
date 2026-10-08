<?php

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\Fixtures\Livewire\MediaForm;
use FilamentTiptapEditor\FilamentTiptapEditorServiceProvider;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

function panelSettings(array $overrides = []): array
{
    return [
        'acceptedFileTypes' => ['image/png'],
        'defaultSort' => 'desc',
        'directory' => 'pictures',
        'diskName' => 'public',
        'imageCropAspectRatio' => null,
        'imageResizeMode' => null,
        'imageResizeTargetWidth' => null,
        'imageResizeTargetHeight' => null,
        'isLimitedToDirectory' => false,
        'isTenantAware' => true,
        'tenantOwnershipRelationshipName' => 'tenant',
        'isMultiple' => true,
        'maxItems' => null,
        'maxSize' => 100,
        'maxWidth' => null,
        'minSize' => 0,
        'pathGenerator' => null,
        'rules' => [],
        'selected' => [],
        'shouldPreserveFilenames' => false,
        'statePath' => 'data.media',
        'types' => ['image/png'],
        'visibility' => 'public',
        ...$overrides,
    ];
}

function forgedSettings(): array
{
    return panelSettings([
        'acceptedFileTypes' => ['text/html'],
        'directory' => 'elsewhere',
        'diskName' => 'local',
        'maxSize' => 999999,
        'types' => [],
        'visibility' => 'private',
    ]);
}

function expectDefaultPanelSettings($component): void
{
    $component
        ->assertSet('acceptedFileTypes', [])
        ->assertSet('directory', 'media')
        ->assertSet('diskName', 'public')
        ->assertSet('maxSize', null)
        ->assertSet('visibility', 'public');
}

beforeEach(function () {
    Storage::fake('public');
});

test('the panel applies settings encrypted by the opener', function () {
    Livewire::test(CuratorPanel::class)
        ->dispatch('open-modal', id: 'curator-panel', settings: CuratorPanel::encryptSettings(panelSettings()))
        ->assertSet('acceptedFileTypes', ['image/png'])
        ->assertSet('directory', 'pictures')
        ->assertSet('maxSize', 100)
        ->assertSet('statePath', 'data.media');
});

test('dispatching open-modal with plain settings changes nothing', function () {
    expectDefaultPanelSettings(
        Livewire::test(CuratorPanel::class)
            ->dispatch('open-modal', id: 'curator-panel', settings: forgedSettings())
    );
});

test('calling openModal directly with plain settings changes nothing', function () {
    expectDefaultPanelSettings(
        Livewire::test(CuratorPanel::class)
            ->call('openModal', 'curator-panel', forgedSettings())
    );
});

test('settings that are not a valid payload change nothing', function (Closure $payload) {
    expectDefaultPanelSettings(
        Livewire::test(CuratorPanel::class)
            ->call('openModal', 'curator-panel', $payload())
    );
})->with([
    'random string' => fn () => fn () => 'not-a-payload',
    'altered ciphertext' => fn () => function () {
        $payload = json_decode(base64_decode(CuratorPanel::encryptSettings(forgedSettings())), true);
        $payload['value'] = base64_encode(str_repeat('A', 48));

        return base64_encode(json_encode($payload));
    },
    'encrypted with another key' => fn () => fn () => (new Illuminate\Encryption\Encrypter(random_bytes(32), 'aes-256-cbc'))
        ->encryptString(json_encode(['purpose' => 'curator-panel-settings', 'expires' => now()->addHour()->getTimestamp(), 'settings' => forgedSettings()])),
    'other encrypted value' => fn () => fn () => Crypt::encryptString(json_encode(['settings' => forgedSettings()])),
]);

test('an expired settings payload changes nothing', function () {
    $payload = CuratorPanel::encryptSettings(forgedSettings());

    $this->travel(2)->hours();

    expectDefaultPanelSettings(
        Livewire::test(CuratorPanel::class)->call('openModal', 'curator-panel', $payload)
    );
});

test('the panel configuration cannot be set from the browser', function (string $property, mixed $value) {
    Livewire::test(CuratorPanel::class)->set($property, $value);
})->throws(CannotUpdateLockedPropertyException::class)->with([
    ['acceptedFileTypes', ['text/html']],
    ['diskName', 'local'],
    ['directory', 'elsewhere'],
    ['visibility', 'private'],
    ['types', []],
    ['validationRules', []],
    ['maxSize', 999999],
    ['minSize', 0],
    ['shouldPreserveFilenames', true],
    ['isLimitedToDirectory', false],
    ['isTenantAware', false],
    ['tenantOwnershipRelationshipName', 'other'],
    ['isMultiple', true],
    ['maxItems', 1000],
    ['pathGenerator', 'App\\Generator'],
    ['statePath', 'data.other'],
    ['files', [['id' => 1, 'disk' => 'local', 'path' => '.env']]],
    ['defaultLimit', 100000],
]);

test('the selection an opener sends is loaded again from the database', function () {
    $media = Media::factory()->create();

    $component = Livewire::test(CuratorPanel::class)
        ->call('openModal', 'curator-panel', CuratorPanel::encryptSettings(panelSettings([
            'selected' => [
                ['id' => $media->id, 'disk' => 'local', 'path' => '.env'],
                ['id' => 999999, 'disk' => 'local', 'path' => '.env'],
            ],
        ])));

    $selected = $component->get('selected');

    expect($selected)->toHaveCount(1)
        ->and($selected[0]['id'])->toBe($media->id)
        ->and($selected[0]['disk'])->toBe($media->disk)
        ->and($selected[0]['path'])->toBe($media->path);
});

test('inserting media sends the stored records, not the selection the browser holds', function () {
    $media = Media::factory()->create();

    Livewire::test(CuratorPanel::class, ['statePath' => 'data.media', 'isMultiple' => true])
        ->set('selected', [
            [...$media->toArray(), 'disk' => 'local', 'path' => '.env', 'url' => 'https://example.com/x'],
            [...$media->toArray(), 'id' => 999999, 'disk' => 'local', 'path' => 'secret.txt'],
        ])
        ->callAction('insertMedia')
        ->assertDispatched('insert-content', function (string $name, array $params) use ($media): bool {
            return $params['statePath'] === 'data.media'
                && count($params['media']) === 1
                && $params['media'][0]['id'] === $media->id
                && $params['media'][0]['disk'] === $media->disk
                && $params['media'][0]['path'] === $media->path;
        });
});

test('the picker opens the panel with an encrypted payload the panel accepts', function () {
    $this->app->register(FilamentTiptapEditorServiceProvider::class);

    $settings = null;

    Livewire::test(MediaForm::class)
        ->call('mountFormComponentAction', 'data.media', 'open_curator_picker')
        ->assertDispatched('open-modal', function (string $name, array $params) use (&$settings): bool {
            $settings = $params['settings'] ?? null;

            return ($params['id'] ?? null) === 'curator-panel' && is_string($settings);
        });

    Livewire::test(CuratorPanel::class)
        ->call('openModal', 'curator-panel', $settings)
        ->assertSet('acceptedFileTypes', ['image/png'])
        ->assertSet('directory', 'pictures')
        ->assertSet('statePath', 'data.media');
});

test('the rich editor media action opens the panel with an encrypted payload the panel accepts', function () {
    $this->app->register(FilamentTiptapEditorServiceProvider::class);

    $settings = null;

    Livewire::test(MediaForm::class)
        ->call('mountFormComponentAction', 'data.content', 'filament_tiptap_media', ['src' => ''])
        ->assertDispatched('open-modal', function (string $name, array $params) use (&$settings): bool {
            $settings = $params['settings'] ?? null;

            return ($params['id'] ?? null) === 'curator-panel' && is_string($settings);
        });

    Livewire::test(CuratorPanel::class)
        ->call('openModal', 'curator-panel', $settings)
        ->assertSet('acceptedFileTypes', ['image/jpeg'])
        ->assertSet('directory', 'editor')
        ->assertSet('statePath', 'data.content');
});
