<?php

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Support\MediaScope;
use Awcodes\Curator\Tests\Fixtures\Livewire\PostForm;
use Awcodes\Curator\Tests\Fixtures\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

function scopedPanel(array $settings = [])
{
    return Livewire::test(CuratorPanel::class)
        ->call('openModal', 'curator-panel', CuratorPanel::encryptSettings([
            'acceptedFileTypes' => ['image/*'],
            'directory' => 'media',
            'diskName' => 'public',
            'isLimitedToDirectory' => false,
            'isMultiple' => true,
            'statePath' => 'data.image_id',
            'types' => ['image/*'],
            'visibility' => 'public',
            'selected' => [],
            ...$settings,
        ]));
}

function listedIds($component): array
{
    return collect($component->get('files'))->pluck('id')->sort()->values()->all();
}

function pickerItem(Media $media): array
{
    return [(string) Str::uuid() => $media->toArray()];
}

beforeEach(function () {
    PostForm::$isAdmin = true;

    Storage::fake('public');
    Storage::fake('local');

    $this->image = Media::factory()->create(['title' => 'shared name']);
    // The factory's type() and disk() are lost once attributes are passed, so they're set as attributes here.
    $this->pdf = Media::factory()->create(['title' => 'shared name', 'type' => 'application/pdf', 'ext' => 'pdf']);
    $this->otherDisk = Media::factory()->create(['title' => 'shared name', 'disk' => 'local']);
});

test('the panel lists only media on its disk with an accepted type', function () {
    expect(listedIds(scopedPanel()))->toBe([$this->image->id]);
});

test('search keeps to the accepted types and the disk', function () {
    $panel = scopedPanel()->set('search', 'shared');

    expect(listedIds($panel))->toBe([$this->image->id]);
});

test('a wildcard type never matches a scriptable type', function () {
    $html = Media::factory()->create(['type' => 'text/html', 'title' => 'shared name']);
    $xhtml = Media::factory()->create(['type' => 'application/xhtml+xml', 'title' => 'shared name']);
    $text = Media::factory()->create(['type' => 'text/plain', 'title' => 'shared name']);

    $panel = scopedPanel(['types' => ['text/*', 'application/*']]);

    expect(listedIds($panel))->toBe([$this->pdf->id, $text->id])
        ->and(listedIds($panel->set('search', 'shared')))->toBe([$this->pdf->id, $text->id]);
});

test('a wildcard is matched as a type group, not a pattern', function () {
    $odd = Media::factory()->create(['type' => 'imageXpng']);

    expect(listedIds(scopedPanel(['types' => ['image_*']])))->toBe([]);
    expect(MediaScope::escapeLike('a_b%c~'))->toBe('a~_b~%c~~');
});

test('a panel limited to its directory lists and searches only that directory', function () {
    Media::factory()->create(['title' => 'shared name', 'directory' => 'elsewhere']);

    $panel = scopedPanel(['isLimitedToDirectory' => true]);

    expect(listedIds($panel))->toBe([$this->image->id])
        ->and(listedIds($panel->set('search', 'shared')))->toBe([$this->image->id]);
});

test('the panel inserts only media within its scope', function () {
    scopedPanel()
        ->set('selected', [$this->image->toArray(), $this->pdf->toArray(), $this->otherDisk->toArray()])
        ->callAction('insertMedia')
        ->assertDispatched('insert-content', fn (string $name, array $params): bool => collect($params['media'])->pluck('id')->all() === [$this->image->id]);
});

test('media the picker held when it opened stays selectable', function () {
    scopedPanel(['selected' => [$this->pdf->id], 'heldIds' => [$this->pdf->id]])
        ->assertSet('selected', fn (array $selected): bool => collect($selected)->pluck('id')->all() === [$this->pdf->id])
        ->callAction('insertMedia')
        ->assertDispatched('insert-content', fn (string $name, array $params): bool => collect($params['media'])->pluck('id')->all() === [$this->pdf->id]);
});

test('the panel actions refuse media outside its scope', function () {
    scopedPanel()
        ->mountAction('download', ['item' => ['id' => $this->otherDisk->id]])
        ->callMountedAction()
        ->assertNoFileDownloaded();
});

test('the picker drops media outside its scope inserted from the browser', function () {
    $post = Post::create();

    Livewire::test(PostForm::class, ['post' => $post])
        ->set('data.image_id', pickerItem($this->pdf))
        ->assertSet('data.image_id', [])
        ->call('save');

    expect($post->fresh()->image_id)->toBeNull();
});

test('the picker keeps media inside its scope', function () {
    $post = Post::create();

    Livewire::test(PostForm::class, ['post' => $post])
        ->set('data.image_id', pickerItem($this->image))
        ->call('save');

    expect($post->fresh()->image_id)->toBe($this->image->id);
});

test('a saved value outside the scope keeps loading and saving', function () {
    $post = Post::create(['image_id' => $this->pdf->id]);

    $component = Livewire::test(PostForm::class, ['post' => $post]);

    expect(collect($component->get('data.image_id'))->pluck('id')->all())->toBe([$this->pdf->id]);

    $component->set('data.title', 'changed')->call('save');

    expect($post->fresh()->image_id)->toBe($this->pdf->id);
});

test('a saved value whose media is gone does not load', function () {
    $post = Post::create(['image_id' => 999999]);

    $component = Livewire::test(PostForm::class, ['post' => $post]);

    expect($component->get('data.image_id'))->toBe([]);
});

test('a saved id only counts for the field it was saved for', function () {
    // The pdf is saved on the record, but for a different field: it isn't in this field's scope.
    $post = Post::create(['gallery' => [['image' => $this->pdf->id, 'caption' => 'x']]]);

    Livewire::test(PostForm::class, ['post' => $post])
        ->set('data.image_id', pickerItem($this->pdf))
        ->assertSet('data.image_id', []);
});

test('a repeater picker keeps media saved in its own place', function () {
    $post = Post::create(['gallery' => [['image' => $this->pdf->id, 'caption' => 'x']]]);

    $component = Livewire::test(PostForm::class, ['post' => $post]);
    $key = array_key_first($component->get('data.gallery'));

    expect(collect($component->get("data.gallery.{$key}.image"))->pluck('id')->all())->toBe([$this->pdf->id]);

    $component->set('data.title', 'changed')->call('save');

    expect($post->fresh()->gallery[0]['image'])->toBe($this->pdf->id);
});

test('ids saved under other keys of a repeater item do not count', function () {
    $post = Post::create(['gallery' => [['image' => null, 'caption' => 'x', 'extra' => ['image' => $this->pdf->id]]]]);

    $component = Livewire::test(PostForm::class, ['post' => $post]);
    $key = array_key_first($component->get('data.gallery'));

    $component->set("data.gallery.{$key}.image", pickerItem($this->pdf))
        ->assertSet("data.gallery.{$key}.image", []);
});

test('a builder picker keeps media saved in its own block', function () {
    $post = Post::create(['content' => [['type' => 'hero', 'data' => ['image' => $this->pdf->id]]]]);

    $component = Livewire::test(PostForm::class, ['post' => $post]);
    $key = array_key_first($component->get('data.content'));

    expect(collect($component->get("data.content.{$key}.data.image"))->pluck('id')->all())->toBe([$this->pdf->id]);
});

test('ids saved in another block type do not count', function () {
    $post = Post::create(['content' => [
        ['type' => 'hero', 'data' => ['image' => null]],
        ['type' => 'text', 'data' => ['body' => 'x', 'image' => $this->pdf->id]],
    ]]);

    $component = Livewire::test(PostForm::class, ['post' => $post]);
    $key = array_key_first($component->get('data.content'));

    $component->set("data.content.{$key}.data.image", pickerItem($this->pdf))
        ->assertSet("data.content.{$key}.data.image", []);
});

test('an item of a block hidden from some users does not count as saved', function () {
    // Saved while the block was hidden, so the builder stored the item as sent, without the picker's checks.
    $post = Post::create(['content' => [['type' => 'admin', 'data' => ['image' => $this->pdf->id]]]]);

    PostForm::$isAdmin = true;

    $component = Livewire::test(PostForm::class, ['post' => $post]);
    $key = array_key_first($component->get('data.content'));

    expect($component->get("data.content.{$key}.data.image"))->toBe([]);
});

test('an item of a block shown on another field\'s value does not count as saved', function () {
    $post = Post::create(['title' => 'shown', 'content' => [['type' => 'gated', 'data' => ['image' => $this->pdf->id]]]]);

    $component = Livewire::test(PostForm::class, ['post' => $post]);
    $key = array_key_first($component->get('data.content'));

    expect($component->get("data.content.{$key}.data.image"))->toBe([]);
});

test('a block hidden from the current user is saved as sent', function () {
    // The reason for the rule above: Filament's builder skips a hidden block's fields when it saves.
    PostForm::$isAdmin = false;

    $post = Post::create();

    Livewire::test(PostForm::class, ['post' => $post])
        ->set('data.content', ['item' => ['type' => 'admin', 'data' => ['image' => $this->pdf->id]]])
        ->call('save');

    expect($post->fresh()->content[0]['data']['image'])->toBe($this->pdf->id);
});

test('a picker in a group with its own state path keeps media saved in its place', function () {
    $post = Post::create(['meta' => ['cover' => $this->pdf->id, 'planted' => ['cover' => $this->image->id]]]);

    $component = Livewire::test(PostForm::class, ['post' => $post]);

    expect(collect($component->get('data.meta.cover'))->pluck('id')->all())->toBe([$this->pdf->id]);
});

test('a picker with a dotted name keeps media saved in its place only', function () {
    $post = Post::create(['settings' => ['logo' => $this->pdf->id]]);

    $component = Livewire::test(PostForm::class, ['post' => $post]);

    expect(collect($component->get('data.settings.logo'))->pluck('id')->all())->toBe([$this->pdf->id]);

    $other = Post::create(['settings' => ['nested' => ['logo' => $this->pdf->id]]]);

    Livewire::test(PostForm::class, ['post' => $other])
        ->set('data.settings.logo', pickerItem($this->pdf))
        ->assertSet('data.settings.logo', []);
});

test('scope resolution keeps the order of the ids and skips unknown ones', function () {
    $second = Media::factory()->create();
    $scope = new MediaScope(disk: 'public', acceptedFileTypes: ['image/*']);

    expect($scope->resolve([$second->id, 999999, $this->image->id, $this->pdf->id])->pluck('id')->all())
        ->toBe([$second->id, $this->image->id])
        ->and($scope->resolve([$this->pdf->id], [$this->pdf->id])->pluck('id')->all())->toBe([$this->pdf->id])
        ->and($scope->contains([$this->image->id, $this->pdf->id]))->toBeFalse();
});
