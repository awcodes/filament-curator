<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\EditMedia;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    $this->storagePath = sys_get_temp_dir() . '/curator-curation-paths-' . Str::random(12);
    File::ensureDirectoryExists($this->storagePath . '/app');
    $this->app->useStoragePath($this->storagePath);

    Storage::fake('public');
    Storage::fake('local');

    Storage::disk('public')->put('media/mine.jpg', 'mine');
    Storage::disk('public')->put('media/mine/hero.jpg', 'hero');
    Storage::disk('public')->put('media/other.jpg', 'other');
    Storage::disk('public')->put('media/other/hero.jpg', 'other hero');
    Storage::disk('local')->put('secrets/x.txt', 'secret');

    $this->media = makeMedia(['directory' => 'media', 'name' => 'mine', 'path' => 'media/mine.jpg']);
});

afterEach(function () {
    File::deleteDirectory($this->storagePath);
});

function curationPathItem(string $disk, string $path, string $key = 'hero'): array
{
    return ['curation' => [
        'key' => $key,
        'disk' => $disk,
        'directory' => 'mine',
        'visibility' => 'public',
        'name' => $key . '.jpg',
        'path' => $path,
        'width' => 10,
        'height' => 10,
        'size' => 4,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
        'url' => '/storage/' . $path,
    ]];
}

function storeCurationsDirectly(Media $media, array $items): Media
{
    DB::table($media->getTable())->where('id', $media->id)->update(['curations' => json_encode($items)]);

    return $media->refresh();
}

function saveCurationsThroughEditPage(Media $media, array $items): Media
{
    $keyed = [];

    foreach ($items as $item) {
        $keyed[(string) Str::uuid()] = $item;
    }

    Livewire::test(EditMedia::class, ['record' => $media->getKey()])
        ->set('data.curations', $keyed)
        ->call('save')
        ->assertHasNoFormErrors();

    return $media->refresh();
}

it('keeps a curation inside the item\'s own folder when the edit page is saved', function () {
    $media = saveCurationsThroughEditPage($this->media, [curationPathItem('public', 'media/mine/hero.jpg')]);

    expect($media->getCuration('hero')['path'])->toBe('media/mine/hero.jpg');
});

it('drops curations that point anywhere else when the edit page is saved', function (string $disk, string $path) {
    $media = saveCurationsThroughEditPage($this->media, [
        curationPathItem('public', 'media/mine/hero.jpg'),
        curationPathItem($disk, $path, 'planted'),
    ]);

    expect($media->hasCuration('hero'))->toBeTrue()
        ->and($media->hasCuration('planted'))->toBeFalse()
        ->and($media->curations)->toHaveCount(1);
})->with([
    'another disk' => ['local', 'secrets/x.txt'],
    'another item\'s file' => ['public', 'media/other.jpg'],
    'another item\'s curation' => ['public', 'media/other/hero.jpg'],
    'a traversal out of the folder' => ['public', 'media/mine/../other.jpg'],
    'a nested path' => ['public', 'media/mine/deeper/hero.jpg'],
    'the folder itself' => ['public', 'media/mine/'],
]);

it('keeps an entry already stored on the item when the edit page is saved', function () {
    $stored = curationPathItem('public', 'media/renamed-before/hero.jpg');
    Storage::disk('public')->put('media/renamed-before/hero.jpg', 'legacy');

    $media = storeCurationsDirectly($this->media, [$stored]);
    $media = saveCurationsThroughEditPage($media, [$stored]);

    expect($media->getCuration('hero')['path'])->toBe('media/renamed-before/hero.jpg');
});

it('does not delete files outside the item\'s curation folder when the item is deleted', function () {
    $media = storeCurationsDirectly($this->media, [
        curationPathItem('public', 'media/mine/hero.jpg'),
        curationPathItem('public', 'media/other.jpg', 'planted'),
        curationPathItem('public', 'media/mine/../other/hero.jpg', 'traversal'),
    ]);

    $media->delete();

    expect(Storage::disk('public')->exists('media/mine/hero.jpg'))->toBeFalse()
        ->and(Storage::disk('public')->exists('media/other.jpg'))->toBeTrue()
        ->and(Storage::disk('public')->exists('media/other/hero.jpg'))->toBeTrue();
});

it('does not move curation files when the item is renamed', function () {
    $media = storeCurationsDirectly($this->media, [
        curationPathItem('public', 'media/mine/hero.jpg'),
        curationPathItem('public', 'media/mine/../other/hero.jpg', 'traversal'),
    ]);

    $media->update(['name' => 'renamed']);

    expect(Storage::disk('public')->exists('media/renamed.jpg'))->toBeTrue()
        ->and(Storage::disk('public')->exists('media/mine/hero.jpg'))->toBeTrue()
        ->and(Storage::disk('public')->get('media/other/hero.jpg'))->toBe('other hero');
});

it('deletes the item\'s own curation folder when the item is deleted', function () {
    $this->media->delete();

    expect(Storage::disk('public')->exists('media/mine/hero.jpg'))->toBeFalse()
        ->and(Storage::disk('public')->exists('media/other/hero.jpg'))->toBeTrue();
});

it('does not delete a folder holding another item\'s file when an item of that name is deleted', function () {
    Storage::disk('public')->put('avatars.jpg', 'root');
    Storage::disk('public')->put('avatars/victim.jpg', 'victim');
    $rooted = makeMedia(['directory' => null, 'name' => 'avatars', 'path' => 'avatars.jpg']);
    makeMedia(['directory' => 'avatars', 'name' => 'victim', 'path' => 'avatars/victim.jpg']);

    $rooted->delete();

    expect(Storage::disk('public')->exists('avatars.jpg'))->toBeFalse()
        ->and(Storage::disk('public')->get('avatars/victim.jpg'))->toBe('victim');
});

it('treats like wildcards in an item\'s name literally when checking its curation folder', function () {
    Storage::disk('public')->put('a_b.jpg', 'root');
    Storage::disk('public')->put('a_b/hero.jpg', 'hero');
    $media = makeMedia(['directory' => null, 'name' => 'a_b', 'path' => 'a_b.jpg']);
    makeMedia(['directory' => 'axb', 'name' => 'other', 'path' => 'axb/other.jpg']);

    expect($media->hasAnotherMediaFileIn('a_b'))->toBeFalse()
        ->and($media->hasAnotherMediaFileIn('axb'))->toBeTrue();
});

it('does not build a url for a stored curation on another disk or outside the item\'s directory', function (string $disk, string $path) {
    $media = storeCurationsDirectly($this->media, [curationPathItem($disk, $path, 'planted')]);

    expect(Blade::render('<x-curator-curation :media="$media" curation="planted" />', ['media' => $media]))->toBe('');

    $items = Livewire::test(EditMedia::class, ['record' => $media->getKey()])->get('data.curations');

    expect(array_values($items)[0]['curation']['url'])->toBe('/storage/' . $path);
})->with([
    'another disk' => ['local', 'secrets/x.txt'],
    'outside the directory' => ['public', 'elsewhere/hero.jpg'],
    'a traversal' => ['public', 'media/mine/../../elsewhere.jpg'],
]);

it('still builds urls for the item\'s own curations', function () {
    $media = storeCurationsDirectly($this->media, [curationPathItem('public', 'media/mine/hero.jpg')]);

    expect(Blade::render('<x-curator-curation :media="$media" curation="hero" />', ['media' => $media]))
        ->toContain('src="/storage/media/mine/hero.jpg"');
});

it('does not let an item take another item\'s name, so no two items share a curation folder', function () {
    Storage::disk('public')->put('media/mine.png', 'png');
    $twin = makeMedia(['directory' => 'media', 'name' => 'twin', 'path' => 'media/twin.png', 'ext' => 'png']);

    $twin->update(['name' => 'mine']);

    expect($twin->refresh()->name)->not->toBe('mine')
        ->and($twin->name)->toStartWith('mine-');
});

it('does not let an item take the name of an existing directory', function () {
    Storage::disk('public')->put('avatars/someone.jpg', 'avatar');
    Storage::disk('public')->put('rooted.jpg', 'root');
    $rooted = makeMedia(['directory' => null, 'name' => 'rooted', 'path' => 'rooted.jpg']);

    $rooted->update(['name' => 'avatars']);

    expect($rooted->refresh()->name)->toStartWith('avatars-');
});

it('never deletes, moves or links another item\'s file, even inside the item\'s own folder', function () {
    Storage::disk('public')->put('avatars.jpg', 'root');
    Storage::disk('public')->put('avatars/victim.jpg', 'victim');
    $attacker = makeMedia(['directory' => null, 'name' => 'avatars', 'path' => 'avatars.jpg']);
    makeMedia(['directory' => 'avatars', 'name' => 'victim', 'path' => 'avatars/victim.jpg']);

    $saved = saveCurationsThroughEditPage($attacker, [curationPathItem('public', 'avatars/victim.jpg', 'planted')]);

    expect($saved->hasCuration('planted'))->toBeFalse();

    $planted = storeCurationsDirectly($attacker, [curationPathItem('public', 'avatars/victim.jpg', 'planted')]);

    expect(Blade::render('<x-curator-curation :media="$media" curation="planted" />', ['media' => $planted]))->toBe('');

    $planted->update(['name' => 'renamed']);

    expect(Storage::disk('public')->get('avatars/victim.jpg'))->toBe('victim');

    $planted->refresh()->delete();

    expect(Storage::disk('public')->get('avatars/victim.jpg'))->toBe('victim');
});

it('refuses to save a curation over another item\'s file', function () {
    $image = imagecreatetruecolor(20, 10);
    ob_start();
    imagepng($image);
    Storage::disk('public')->put('avatars.png', ob_get_clean());
    Storage::disk('public')->put('avatars/victim.png', 'victim');
    $attacker = makeMedia(['directory' => null, 'name' => 'avatars', 'path' => 'avatars.png', 'ext' => 'png', 'type' => 'image/png', 'width' => 20, 'height' => 10]);
    makeMedia(['directory' => 'avatars', 'name' => 'victim', 'path' => 'avatars/victim.png', 'ext' => 'png']);

    Livewire::test(Awcodes\Curator\Components\Modals\CuratorCuration::class, [
        'media' => $attacker,
        'modalId' => 'curation',
        'statePath' => 'data.curation',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ])->call('saveCuration', [
        'key' => 'victim', 'format' => 'png', 'quality' => 60, 'x' => 0, 'y' => 0, 'width' => 20, 'height' => 10,
        'rotate' => 0, 'scaleX' => 1, 'scaleY' => 1, 'canvasData' => ['width' => 20, 'height' => 10, 'naturalWidth' => 20, 'naturalHeight' => 10],
    ])->assertHasErrors(['key']);

    expect(Storage::disk('public')->get('avatars/victim.png'))->toBe('victim');
});

it('does not build a url for a stored curation of root media outside a curation folder', function (string $path) {
    Storage::disk('public')->put('rooted.jpg', 'root');
    $rooted = makeMedia(['directory' => null, 'name' => 'rooted', 'path' => 'rooted.jpg']);
    $rooted = storeCurationsDirectly($rooted, [curationPathItem('public', $path, 'planted')]);

    expect(Blade::render('<x-curator-curation :media="$media" curation="planted" />', ['media' => $rooted]))->toBe('');
})->with([
    'a document' => ['private-stuff/payroll.pdf'],
    'a deeper image' => ['private-stuff/2026/scan.jpg'],
    'a file in the root' => ['other.jpg'],
]);

it('sets the disk and url of a saved curation on the server', function () {
    $item = curationPathItem('public', 'media/mine/hero.jpg');
    $item['curation']['url'] = 'https://evil.example/x.jpg';
    $item['curation']['visibility'] = 'nonsense';

    $curation = saveCurationsThroughEditPage($this->media, [$item])->getCuration('hero');

    expect($curation['disk'])->toBe('public')
        ->and($curation['visibility'])->toBe('public')
        ->and($curation['url'])->toBe('/storage/media/mine/hero.jpg')
        ->and(Blade::render('<x-curator-curation :media="$media" curation="hero" />', ['media' => $this->media->refresh()]))
        ->toContain('src="/storage/media/mine/hero.jpg"');
});

it('keeps curations of media whose directory has a trailing slash', function () {
    $media = makeMedia(['directory' => 'media/', 'name' => 'slashed', 'path' => 'media/slashed.jpg']);

    $saved = saveCurationsThroughEditPage($media, [curationPathItem('public', 'media/slashed/hero.jpg')]);

    expect($saved->getCuration('hero')['path'])->toBe('media/slashed/hero.jpg');
});

it('gives a curation with no disk the item\'s own disk when the edit page is saved', function () {
    $item = curationPathItem('public', 'media/mine/hero.jpg');
    $item['curation']['disk'] = null;

    expect(saveCurationsThroughEditPage($this->media, [$item])->getCuration('hero')['disk'])->toBe('public');
});

it('lets the modal save a curation again under the same key', function () {
    $image = imagecreatetruecolor(20, 10);
    ob_start();
    imagepng($image);
    Storage::disk('public')->put('media/pic.png', ob_get_clean());
    $media = makeMedia(['directory' => 'media', 'name' => 'pic', 'path' => 'media/pic.png', 'ext' => 'png', 'type' => 'image/png', 'width' => 20, 'height' => 10]);

    $component = Livewire::test(Awcodes\Curator\Components\Modals\CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.curation',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ]);

    $payload = [
        'key' => 'thumb', 'format' => 'png', 'quality' => 60, 'x' => 0, 'y' => 0, 'width' => 20, 'height' => 10,
        'rotate' => 0, 'scaleX' => 1, 'scaleY' => 1, 'canvasData' => ['width' => 20, 'height' => 10, 'naturalWidth' => 20, 'naturalHeight' => 10],
    ];

    $component->call('saveCuration', $payload)->assertHasNoErrors();
    $component->call('saveCuration', $payload)->assertHasNoErrors();

    expect(Storage::disk('public')->exists('media/pic/thumb.png'))->toBeTrue();
});

it('sees other items through global scopes, such as a tenant scope', function () {
    Storage::disk('public')->put('avatars.jpg', 'root');
    Storage::disk('public')->put('avatars/victim.jpg', 'victim');
    $attacker = makeMedia(['directory' => null, 'name' => 'avatars', 'path' => 'avatars.jpg']);
    $victim = makeMedia(['directory' => 'avatars', 'name' => 'victim', 'path' => 'avatars/victim.jpg']);

    Media::addGlobalScope('only-attacker', fn ($query) => $query->whereKeyNot($victim->getKey()));

    try {
        expect($attacker->isAnotherMediaFile('avatars/victim.jpg'))->toBeTrue()
            ->and(Media::isNameTaken('public', 'avatars', 'victim'))->toBeTrue();

        $planted = storeCurationsDirectly($attacker, [curationPathItem('public', 'avatars/victim.jpg', 'planted')]);
        $planted->delete();

        expect(Storage::disk('public')->get('avatars/victim.jpg'))->toBe('victim');
    } finally {
        (fn () => static::$globalScopes[Media::class] = [])->call(new Media);
    }
});

it('matches another item\'s file and name regardless of case', function () {
    makeMedia(['directory' => 'Media', 'name' => 'Victim', 'path' => 'Media/Victim.jpg']);

    expect($this->media->isAnotherMediaFile('media/victim.jpg'))->toBeTrue()
        ->and(Media::isNameTaken('public', 'Media', 'victim'))->toBeTrue();
});

it('matches another item\'s name however its directory is spelled', function (string $stored) {
    makeMedia(['directory' => $stored, 'name' => 'spelled', 'path' => 'media/spelled.jpg']);

    expect(Media::isNameTaken('public', 'media', 'spelled'))->toBeTrue();
})->with(['media/', '/media', '/media/']);

it('gives an upload a new name when another item in the directory already has its name, whatever the extension', function () {
    expect(Media::uniqueName('public', 'media', 'mine', 'png'))->toStartWith('mine-')
        ->and(Media::uniqueName('public', 'media', 'fresh', 'png'))->toBe('fresh');
});

it('cannot reach another item\'s curations by sharing its name', function () {
    Storage::disk('public')->put('media/sneaky.png', 'png');
    $sneaky = makeMedia(['directory' => 'media', 'name' => 'sneaky', 'path' => 'media/sneaky.png', 'ext' => 'png']);
    Storage::disk('public')->delete('media/mine.jpg');

    $sneaky->update(['name' => 'mine']);

    expect($sneaky->refresh()->name)->toStartWith('mine-');

    $saved = saveCurationsThroughEditPage($sneaky, [curationPathItem('public', 'media/mine/hero.jpg', 'planted')]);
    $saved->delete();

    expect(Storage::disk('public')->get('media/mine/hero.jpg'))->toBe('hero');
});
