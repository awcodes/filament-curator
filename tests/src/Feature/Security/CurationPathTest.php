<?php

use Awcodes\Curator\Components\Modals\CuratorCuration;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\PathGenerators\DatePathGenerator;
use Awcodes\Curator\Resources\MediaResource\EditMedia;
use Awcodes\Curator\Resources\MediaResource\ListMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');

    Storage::disk('public')->put('media/mine.jpg', 'mine');
    Storage::disk('public')->put('media/mine/hero.jpg', 'hero');
    Storage::disk('public')->put('media/other.jpg', 'other');
    Storage::disk('public')->put('media/other/hero.jpg', 'other hero');
    Storage::disk('local')->put('secrets/x.txt', 'secret');

    $this->media = curationPathMedia(['directory' => 'media', 'name' => 'mine', 'path' => 'media/mine.jpg']);
    curationPathMedia(['directory' => 'media', 'name' => 'other', 'path' => 'media/other.jpg']);
});

function curationPathMedia(array $attributes): Media
{
    return Media::create(array_merge([
        'disk' => 'public',
        'directory' => 'media',
        'visibility' => 'public',
        'width' => 10,
        'height' => 10,
        'size' => 4,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ], $attributes));
}

function curationPathItem(?string $disk, string $path, string $key = 'hero'): array
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

function renderCuration(Media $media, string $key): string
{
    return trim(Blade::render('<x-curator-curation :media="$media" curation="' . $key . '" />', ['media' => $media]));
}

function pngBytes(int $width = 20, int $height = 10): string
{
    $image = imagecreatetruecolor($width, $height);
    ob_start();
    imagepng($image);

    return (string) ob_get_clean();
}

function curationModalPayload(string $key): array
{
    return [
        'key' => $key, 'format' => 'png', 'quality' => 60, 'x' => 0, 'y' => 0, 'width' => 20, 'height' => 10,
        'rotate' => 0, 'scaleX' => 1, 'scaleY' => 1, 'canvasData' => ['width' => 20, 'height' => 10, 'naturalWidth' => 20, 'naturalHeight' => 10],
    ];
}

function curationModal(Media $media)
{
    return Livewire::test(CuratorCuration::class, [
        'media' => $media,
        'modalId' => 'curation',
        'statePath' => 'data.curation',
        'presets' => [],
        'formats' => config('curator.curation_formats'),
    ]);
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
    'a file in the item\'s own folder that isn\'t an image' => ['public', 'media/mine/notes.pdf'],
]);

it('keeps an entry already stored on the item when the edit page is saved', function () {
    $stored = curationPathItem('public', 'media/renamed-before/hero.jpg');
    Storage::disk('public')->put('media/renamed-before/hero.jpg', 'legacy');

    $media = storeCurationsDirectly($this->media, [$stored]);
    $media = saveCurationsThroughEditPage($media, [$stored]);

    expect($media->getCuration('hero')['path'])->toBe('media/renamed-before/hero.jpg');
});

it('does not build a url for a stored curation on another disk or outside the item\'s directory', function (string $disk, string $path) {
    Storage::disk('public')->put('elsewhere/hero.jpg', 'elsewhere');
    Storage::disk('public')->put('elsewhere.jpg', 'elsewhere');
    $planted = curationPathItem($disk, $path, 'planted');
    $planted['curation']['url'] = 'stored-url-left-alone';
    $media = storeCurationsDirectly($this->media, [$planted]);

    expect(renderCuration($media, 'planted'))->toBe('');
})->with([
    'another disk' => ['local', 'secrets/x.txt'],
    'outside the directory' => ['public', 'elsewhere/hero.jpg'],
    'a traversal' => ['public', 'media/mine/../../elsewhere.jpg'],
]);

it('still builds urls for the item\'s own curations', function () {
    $media = storeCurationsDirectly($this->media, [curationPathItem('public', 'media/mine/hero.jpg')]);

    expect(renderCuration($media, 'hero'))->toContain('src="/storage/media/mine/hero.jpg"');
});

it('does not let an item take another item\'s name, so no two items share a curation folder', function () {
    Storage::disk('public')->put('media/twin.png', 'png');
    $twin = curationPathMedia(['directory' => 'media', 'name' => 'twin', 'path' => 'media/twin.png', 'ext' => 'png']);

    $twin->update(['name' => 'mine']);

    expect($twin->refresh()->name)->not->toBe('mine')
        ->and($twin->name)->toStartWith('mine-');
});

it('does not let an item take the name of an existing directory', function () {
    Storage::disk('public')->put('avatars/someone.jpg', 'avatar');
    Storage::disk('public')->put('rooted.jpg', 'root');
    $rooted = curationPathMedia(['directory' => '', 'name' => 'rooted', 'path' => 'rooted.jpg']);

    $rooted->update(['name' => 'avatars']);

    expect($rooted->refresh()->name)->toStartWith('avatars-');

    $rooted->delete();

    expect(Storage::disk('public')->get('avatars/someone.jpg'))->toBe('avatar');
});

it('never links another item\'s file, even inside the item\'s own folder', function () {
    Storage::disk('public')->put('avatars.jpg', 'root');
    Storage::disk('public')->put('avatars/victim.jpg', 'victim');
    $attacker = curationPathMedia(['directory' => '', 'name' => 'avatars', 'path' => 'avatars.jpg']);
    curationPathMedia(['directory' => 'avatars', 'name' => 'victim', 'path' => 'avatars/victim.jpg']);

    $saved = saveCurationsThroughEditPage($attacker, [curationPathItem('public', 'avatars/victim.jpg', 'planted')]);

    expect($saved->hasCuration('planted'))->toBeFalse();

    $planted = storeCurationsDirectly($attacker, [curationPathItem('public', 'avatars/victim.jpg', 'planted')]);

    expect(renderCuration($planted, 'planted'))->toBe('');
});

it('refuses to save a curation over another item\'s file', function () {
    Storage::disk('public')->put('avatars.png', pngBytes());
    Storage::disk('public')->put('avatars/victim.png', 'victim');
    $attacker = curationPathMedia(['directory' => '', 'name' => 'avatars', 'path' => 'avatars.png', 'ext' => 'png', 'type' => 'image/png', 'width' => 20, 'height' => 10]);
    curationPathMedia(['directory' => 'avatars', 'name' => 'victim', 'path' => 'avatars/victim.png', 'ext' => 'png']);

    curationModal($attacker)->call('saveCuration', curationModalPayload('victim'))->assertHasErrors(['key']);

    expect(Storage::disk('public')->get('avatars/victim.png'))->toBe('victim');
});

it('does not build a url for a stored curation of root media outside a curation folder', function (string $path) {
    Storage::disk('public')->put('rooted.jpg', 'root');
    $rooted = curationPathMedia(['directory' => '', 'name' => 'rooted', 'path' => 'rooted.jpg']);
    $rooted = storeCurationsDirectly($rooted, [curationPathItem('public', $path, 'planted')]);

    expect(renderCuration($rooted, 'planted'))->toBe('');
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
        ->and(renderCuration($this->media->refresh(), 'hero'))->toContain('src="/storage/media/mine/hero.jpg"');
});

it('keeps curations of media whose directory has a trailing slash', function () {
    $media = curationPathMedia(['directory' => 'media/', 'name' => 'slashed', 'path' => 'media/slashed.jpg']);

    $saved = saveCurationsThroughEditPage($media, [curationPathItem('public', 'media/slashed/hero.jpg')]);

    expect($saved->getCuration('hero')['path'])->toBe('media/slashed/hero.jpg');
});

it('gives a curation with no disk the item\'s own disk when the edit page is saved', function () {
    $item = curationPathItem(null, 'media/mine/hero.jpg');

    expect(saveCurationsThroughEditPage($this->media, [$item])->getCuration('hero')['disk'])->toBe('public');
});

it('lets the modal save a curation again under the same key', function () {
    Storage::disk('public')->put('media/pic.png', pngBytes());
    $media = curationPathMedia(['directory' => 'media', 'name' => 'pic', 'path' => 'media/pic.png', 'ext' => 'png', 'type' => 'image/png', 'width' => 20, 'height' => 10]);

    $component = curationModal($media);

    $component->call('saveCuration', curationModalPayload('thumb'))->assertHasNoErrors();
    $component->call('saveCuration', curationModalPayload('thumb'))->assertHasNoErrors();

    expect(Storage::disk('public')->exists('media/pic/thumb.png'))->toBeTrue();
});

it('sees other items through global scopes, such as a tenant scope', function () {
    Storage::disk('public')->put('avatars.jpg', 'root');
    Storage::disk('public')->put('avatars/victim.jpg', 'victim');
    $attacker = curationPathMedia(['directory' => '', 'name' => 'avatars', 'path' => 'avatars.jpg']);
    $victim = curationPathMedia(['directory' => 'avatars', 'name' => 'victim', 'path' => 'avatars/victim.jpg']);

    Media::addGlobalScope('only-attacker', fn ($query) => $query->whereKeyNot($victim->getKey()));

    try {
        expect($attacker->isAnotherMediaFile('avatars/victim.jpg'))->toBeTrue()
            ->and(Media::isNameTaken('public', 'avatars', 'victim'))->toBeTrue();
    } finally {
        (fn () => static::$globalScopes[Media::class] = [])->call(new Media);
    }
});

it('matches another item\'s file and name regardless of case', function () {
    curationPathMedia(['directory' => 'Media', 'name' => 'Victim', 'path' => 'Media/Victim.jpg']);

    expect($this->media->isAnotherMediaFile('media/victim.jpg'))->toBeTrue()
        ->and(Media::isNameTaken('public', 'Media', 'victim'))->toBeTrue();
});

it('matches another item\'s name however its directory is spelled', function (string $stored) {
    curationPathMedia(['directory' => $stored, 'name' => 'spelled', 'path' => 'media/spelled.jpg']);

    expect(Media::isNameTaken('public', 'media', 'spelled'))->toBeTrue();
})->with(['media/', '/media', '/media/']);

it('gives an upload a new name when another item in the directory already has its name, whatever the extension', function () {
    expect(Media::uniqueName('public', 'media', 'mine', 'png'))->toStartWith('mine-')
        ->and(Media::uniqueName('public', 'media', 'fresh', 'png'))->toBe('fresh');
});

it('cannot reach another item\'s curations by sharing its name', function () {
    Storage::disk('public')->put('media/sneaky.png', 'png');
    $sneaky = curationPathMedia(['directory' => 'media', 'name' => 'sneaky', 'path' => 'media/sneaky.png', 'ext' => 'png']);
    Storage::disk('public')->delete('media/mine.jpg');

    $sneaky->update(['name' => 'mine']);

    expect($sneaky->refresh()->name)->toStartWith('mine-');

    $saved = saveCurationsThroughEditPage($sneaky, [curationPathItem('public', 'media/mine/hero.jpg', 'planted')]);

    expect($saved->hasCuration('planted'))->toBeFalse();

    $saved->delete();

    expect(Storage::disk('public')->get('media/mine/hero.jpg'))->toBe('hero');
});

it('deletes the item\'s own curation folder when the item is deleted', function () {
    $this->media->delete();

    expect(Storage::disk('public')->exists('media/mine/hero.jpg'))->toBeFalse()
        ->and(Storage::disk('public')->exists('media/other/hero.jpg'))->toBeTrue();
});

it('does not delete a folder holding another item\'s file when an item of that name is deleted', function () {
    Storage::disk('public')->put('avatars.jpg', 'root');
    Storage::disk('public')->put('avatars/victim.jpg', 'victim');
    Storage::disk('public')->put('avatars/victim/hero.jpg', 'victim hero');
    $clash = curationPathMedia(['directory' => '', 'name' => 'avatars', 'path' => 'avatars.jpg']);
    curationPathMedia(['directory' => 'avatars', 'name' => 'victim', 'path' => 'avatars/victim.jpg']);

    $clash->delete();

    expect(Storage::disk('public')->exists('avatars.jpg'))->toBeFalse()
        ->and(Storage::disk('public')->get('avatars/victim.jpg'))->toBe('victim')
        ->and(Storage::disk('public')->get('avatars/victim/hero.jpg'))->toBe('victim hero');
});

it('treats like wildcards in an item\'s name literally when checking its curation folder', function () {
    Storage::disk('public')->put('media/a_c.jpg', 'a_c');
    Storage::disk('public')->put('media/a_c/hero.jpg', 'own');
    Storage::disk('public')->put('media/abc.jpg', 'abc');
    $wild = curationPathMedia(['directory' => 'media', 'name' => 'a_c', 'path' => 'media/a_c.jpg']);
    curationPathMedia(['directory' => 'media', 'name' => 'abc', 'path' => 'media/abc/x.jpg']);

    $wild->delete();

    expect(Storage::disk('public')->exists('media/a_c/hero.jpg'))->toBeFalse();
});

it('keeps a swapped file in the item\'s own folder, so the item never moves in beside another item of its name', function () {
    config(['curator.path_generator' => DatePathGenerator::class]);
    $today = 'media/' . now()->format('Y/m/d');
    Storage::disk('public')->put($today . '/photo.png', 'victim');
    Storage::disk('public')->put($today . '/photo/thumb.webp', 'victim thumb');
    curationPathMedia(['directory' => $today, 'name' => 'photo', 'path' => $today . '/photo.png', 'ext' => 'png']);
    Storage::disk('public')->put('media/2020/01/01/old.jpg', 'old');
    $old = curationPathMedia(['directory' => 'media/2020/01/01', 'name' => 'old', 'path' => 'media/2020/01/01/old.jpg']);

    Livewire::test(EditMedia::class, ['record' => $old->getKey()])->set('data.name', 'photo')->call('save')->assertHasNoFormErrors();

    expect($old->refresh()->name)->toBe('photo');

    Livewire::test(EditMedia::class, ['record' => $old->getKey()])
        ->set('data.file', UploadedFile::fake()->image('anything.png', 30, 10))
        ->call('save')
        ->assertHasNoFormErrors();

    $old->refresh();

    expect($old->directory)->toBe('media/2020/01/01')
        ->and($old->path)->toBe('media/2020/01/01/photo.png')
        ->and(Storage::disk('public')->exists('media/2020/01/01/photo.png'))->toBeTrue()
        ->and(Storage::disk('public')->exists('media/2020/01/01/old.jpg'))->toBeFalse()
        ->and(Storage::disk('public')->get($today . '/photo.png'))->toBe('victim');

    $saved = saveCurationsThroughEditPage($old, [curationPathItem('public', $today . '/photo/thumb.webp', 'planted')]);

    expect($saved->hasCuration('planted'))->toBeFalse();

    $saved->delete();

    expect(Storage::disk('public')->get($today . '/photo/thumb.webp'))->toBe('victim thumb');
});

it('keeps curations resolving after the file is swapped for one with another extension', function () {
    $media = storeCurationsDirectly($this->media, [curationPathItem('public', 'media/mine/hero.jpg')]);
    Storage::disk('public')->put('media/2026/11/upload.png', 'replacement');

    $media->file = ['disk' => 'public', 'directory' => 'media/2026/11', 'name' => 'upload', 'path' => 'media/2026/11/upload.png', 'ext' => 'png'];
    $media->save();
    $media->refresh();

    expect($media->directory)->toBe('media')
        ->and($media->path)->toBe('media/mine.png')
        ->and(Storage::disk('public')->get('media/mine.png'))->toBe('replacement')
        ->and(Storage::disk('public')->exists('media/mine.jpg'))->toBeFalse()
        ->and(renderCuration($media, 'hero'))->toContain('src="/storage/media/mine/hero.jpg"');
});

it('does not hand out the same name twice in one request, as a multi-file upload names every file before saving any', function () {
    $first = Media::uniqueName('public', 'media', 'batch', 'jpg');
    $second = Media::uniqueName('public', 'media', 'batch', 'png');
    $third = Media::uniqueName('public', 'media', 'BATCH', 'webp');

    expect($first)->toBe('batch')
        ->and($second)->toStartWith('batch-')
        ->and($third)->toStartWith('BATCH-')
        ->and($third)->not->toBe($second);
});

it('gives every file of a multi-file upload its own name', function () {
    config(['curator.should_preserve_filenames' => true, 'curator.path_generator' => null]);

    Livewire::test(ListMedia::class)
        ->mountAction('curator_multi_upload')
        ->setActionData(['files' => [
            UploadedFile::fake()->image('batch.jpg', 10, 10),
            UploadedFile::fake()->image('batch.png', 10, 10),
        ]])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $names = Media::query()->where('name', 'like', 'batch%')->pluck('name');

    expect($names)->toHaveCount(2)
        ->and($names->unique())->toHaveCount(2);
});

it('keeps the curation folder of an older row that shares the item\'s name with another extension', function () {
    Storage::disk('public')->put('media/twin.png', 'png');
    Storage::disk('public')->put('media/twin.jpg', 'jpg');
    Storage::disk('public')->put('media/twin/thumb.webp', 'crop');

    $now = now();
    $rows = [];

    foreach (['png', 'jpg'] as $ext) {
        $rows[$ext] = DB::table(app(Media::class)->getTable())->insertGetId([
            'disk' => 'public', 'directory' => 'media', 'visibility' => 'public', 'name' => 'twin',
            'path' => 'media/twin.' . $ext, 'type' => 'image/' . ($ext === 'jpg' ? 'jpeg' : 'png'), 'ext' => $ext,
            'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    Media::find($rows['png'])->delete();

    expect(Storage::disk('public')->exists('media/twin.png'))->toBeFalse()
        ->and(Storage::disk('public')->get('media/twin/thumb.webp'))->toBe('crop');
});

it('gives a file swapped onto another disk a single suffix when its name is taken there', function () {
    Storage::disk('local')->put('uploads/mine.png', 'someone else');
    Storage::disk('local')->put('uploads/upload.png', 'replacement');

    $media = $this->media;
    $media->file = ['disk' => 'local', 'directory' => 'uploads', 'name' => 'upload', 'path' => 'uploads/upload.png', 'ext' => 'png'];
    $media->save();
    $media->refresh();

    expect($media->disk)->toBe('local')
        ->and($media->name)->toMatch('/^mine-\d+$/')
        ->and(Storage::disk('local')->get($media->path))->toBe('replacement')
        ->and(Storage::disk('local')->get('uploads/mine.png'))->toBe('someone else')
        ->and(Storage::disk('public')->exists('media/mine.jpg'))->toBeFalse();
});

it('leaves the original file and row alone when the replacement cannot be moved into place', function () {
    $media = $this->media;
    $media->file = ['disk' => 'public', 'directory' => 'media/2026/11', 'name' => 'missing', 'path' => 'media/2026/11/missing.png', 'ext' => 'png'];

    expect(fn () => $media->save())->toThrow(RuntimeException::class, 'Unable to move the replacement file');

    $stored = Media::find($media->getKey());

    expect(Storage::disk('public')->get('media/mine.jpg'))->toBe('mine')
        ->and($stored->path)->toBe('media/mine.jpg')
        ->and($stored->ext)->toBe('jpg')
        ->and($stored->directory)->toBe('media');
});

it('removes only curation images from the item\'s folder when the item is deleted', function () {
    Storage::disk('public')->put('avatars.jpg', 'root');
    Storage::disk('public')->put('avatars/thumb.webp', 'crop');
    Storage::disk('public')->put('avatars/user-1.pdf', 'app file');
    $avatars = curationPathMedia(['directory' => '', 'name' => 'avatars', 'path' => 'avatars.jpg']);

    $avatars->delete();

    expect(Storage::disk('public')->exists('avatars/thumb.webp'))->toBeFalse()
        ->and(Storage::disk('public')->get('avatars/user-1.pdf'))->toBe('app file');
});

it('removes the item\'s curation folder when it held only curations', function () {
    $this->media->delete();

    expect(Storage::disk('public')->directoryExists('media/mine'))->toBeFalse()
        ->and(Storage::disk('public')->exists('media/other/hero.jpg'))->toBeTrue();
});

it('keeps a curation saved in a format the app configured', function () {
    config(['curator.curation_formats' => [...config('curator.curation_formats'), 'TIF']]);
    Storage::disk('public')->put('media/mine/hero.tif', 'tif');

    $media = saveCurationsThroughEditPage($this->media, [curationPathItem('public', 'media/mine/hero.tif')]);

    expect($media->getCuration('hero')['path'])->toBe('media/mine/hero.tif');
});
