<?php

declare(strict_types=1);

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function makeMedia(array $overrides = []): Media
{
    return Media::create(array_merge([
        'disk' => 'public',
        'directory' => null,
        'visibility' => 'public',
        'name' => 'test-file',
        'path' => 'test-file.jpg',
        'size' => 1000,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ], $overrides));
}
