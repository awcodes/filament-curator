<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\ListMedia;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

// Filament authorizes bulk actions through deleteAny() alone, so a policy that
// only restricts delete() on individual records was bypassed by bulk delete.

test('bulk delete skips records the delete policy denies', function () {
    Storage::fake('public');

    $policy = new class
    {
        public function viewAny(): bool
        {
            return true;
        }

        public function delete(mixed $user, Media $media): bool
        {
            return $media->name !== 'protected';
        }
    };

    Gate::policy(Media::class, $policy::class);

    $allowed = makeMedia(['name' => 'allowed']);
    $protected = makeMedia(['name' => 'protected']);

    Livewire::test(ListMedia::class)
        ->selectTableRecords([$allowed, $protected])
        ->callAction(TestAction::make(DeleteBulkAction::getDefaultName())->table()->bulk());

    expect(Media::find($allowed->id))->toBeNull()
        ->and(Media::find($protected->id))->not->toBeNull();
});
