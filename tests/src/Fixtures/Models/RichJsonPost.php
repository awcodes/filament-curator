<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Models;

/**
 * A RichPost whose content column holds the editor's TipTap JSON document, as `RichEditor::json()` stores it.
 */
class RichJsonPost extends RichPost
{
    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    protected function storesContentAsJson(): bool
    {
        return true;
    }
}
