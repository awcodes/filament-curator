<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Resources\Users\Pages;

use Awcodes\Curator\Tests\Fixtures\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
