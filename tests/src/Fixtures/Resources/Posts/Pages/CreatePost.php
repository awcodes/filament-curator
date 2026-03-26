<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Resources\Posts\Pages;

use Awcodes\Curator\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
