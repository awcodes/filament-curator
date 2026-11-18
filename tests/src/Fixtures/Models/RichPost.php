<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Models;

use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Workbench\App\Models\Post;

/**
 * A post whose content column is registered as rich content with Curator's plugin, as an app does to render it with
 * `renderRichContent()` and to give its RichEditor field the plugin's attachment provider.
 */
class RichPost extends Post implements HasRichContent
{
    use InteractsWithRichContent;

    public static bool $storeMediaIds = false;

    protected $table = 'posts';

    protected function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->json($this->storesContentAsJson())
            ->plugins([AttachCuratorMediaPlugin::make()->storeMediaIds(static::$storeMediaIds)]);
    }

    protected function storesContentAsJson(): bool
    {
        return false;
    }
}
