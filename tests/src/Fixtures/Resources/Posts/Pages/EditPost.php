<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Resources\Posts\Pages;

use Awcodes\Curator\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;
}
