<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Modals\CuratorPanel;
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\EditMedia;
use Awcodes\Curator\Resources\Media\Pages\ListMedia;
use Awcodes\Curator\Tests\Fixtures\Policies\MediaManagementDeniedPolicy;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

// Consuming packages need one place to hook every path that deletes media —
// the resource table's row and bulk actions, the edit page, and the picker
// panel — e.g. to warn that a file is still in use, without reaching for a
// global DeleteAction::configureUsing().

beforeEach(function () {
    Storage::fake('public');

    Curator::configureDeleteActionsUsing(function (Action $action): void {
        $action->modalDescription(fn (?Media $record): string => "Deleting {$record?->name}");

        if ($action instanceof BulkAction) {
            $action->modalDescription(fn (Collection $records): string => "Deleting {$records->count()} files");
        }
    });
});

test('the table row delete action is configured', function () {
    $media = makeMedia(['name' => 'row-target']);

    Livewire::test(ListMedia::class)
        ->mountAction(TestAction::make('delete')->table($media))
        ->assertMountedActionModalSee('Deleting row-target');
});

test('the table bulk delete action is configured', function () {
    $media = collect([makeMedia(['name' => 'one']), makeMedia(['name' => 'two'])]);

    Livewire::test(ListMedia::class)
        ->selectTableRecords($media)
        ->mountAction(TestAction::make(DeleteBulkAction::getDefaultName())->table()->bulk())
        ->assertMountedActionModalSee('Deleting 2 files');
});

test('the edit page delete action is configured', function () {
    $media = makeMedia(['name' => 'page-target']);

    Livewire::test(EditMedia::class, ['record' => $media->id])
        ->mountAction('delete')
        ->assertMountedActionModalSee('Deleting page-target');
});

test('the picker panel destroyItem action is configured with the target as $record', function () {
    $media = makeMedia(['name' => 'panel-target']);

    Livewire::test(CuratorPanel::class)
        ->mountAction('destroyItem', ['item' => ['id' => $media->id]])
        ->assertMountedActionModalSee('Deleting panel-target');
});

test('a configured before hook can stop the picker panel from deleting', function () {
    Curator::configureDeleteActionsUsing(
        fn (Action $action) => $action->before(fn (Action $action) => $action->halt()),
    );

    $media = makeMedia(['name' => 'kept']);

    Livewire::test(CuratorPanel::class)
        ->mountAction('destroyItem', ['item' => ['id' => $media->id]])
        ->callMountedAction();

    expect(Media::find($media->id))->not->toBeNull();
});

test('the picker panel does not resolve a record for every rendered item', function () {
    foreach (range(1, 5) as $i) {
        makeMedia(['name' => "item-{$i}"]);
    }

    DB::enableQueryLog();

    Livewire::test(CuratorPanel::class)->assertOk();

    $lookups = collect(DB::getQueryLog())
        ->filter(fn (array $query): bool => str_contains($query['query'], '"curator"."id" = ?'));

    expect($lookups)->toBeEmpty();
});

// The hook must not weaken the MediaPolicy on any delete path.

test('a configured table row delete action still honours a denying policy', function () {
    Gate::policy(Media::class, MediaManagementDeniedPolicy::class);

    $media = makeMedia(['name' => 'row-protected']);

    Livewire::test(ListMedia::class)
        ->assertActionHidden(TestAction::make('delete')->table($media));
});

test('a configured table bulk delete action still honours a denying policy', function () {
    Gate::policy(Media::class, MediaManagementDeniedPolicy::class);

    $media = collect([makeMedia(['name' => 'bulk-one']), makeMedia(['name' => 'bulk-two'])]);

    Livewire::test(ListMedia::class)
        ->selectTableRecords($media)
        ->assertActionHidden(TestAction::make(DeleteBulkAction::getDefaultName())->table()->bulk());
});

test('a configured edit page delete action still honours a denying policy', function () {
    // The page itself needs update access; only deleting is denied.
    $policy = new class
    {
        public function viewAny(): bool
        {
            return true;
        }

        public function view(): bool
        {
            return true;
        }

        public function update(): bool
        {
            return true;
        }

        public function delete(): bool
        {
            return false;
        }
    };

    Gate::policy(Media::class, $policy::class);

    $media = makeMedia(['name' => 'page-protected']);

    Livewire::test(EditMedia::class, ['record' => $media->id])
        ->assertActionHidden('delete');
});

test('a configured picker panel destroyItem action still honours a denying policy', function () {
    Gate::policy(Media::class, MediaManagementDeniedPolicy::class);

    $media = makeMedia(['name' => 'panel-protected']);

    Livewire::test(CuratorPanel::class)
        ->mountAction('destroyItem', ['item' => ['id' => $media->id]])
        ->assertMountedActionModalDontSee('panel-protected')
        ->callMountedAction();

    expect(Media::find($media->id))->not->toBeNull();
});
