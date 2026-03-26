<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Resources\Posts\Pages;

use Awcodes\Curator\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
