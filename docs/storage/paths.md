---
title: Path generation
description: Control where Curator writes uploaded files, globally or per field.
---

# Path generation

By default Curator writes uploads to the disk and directory set in the config. Path generators change that layout without changing the disk.

## Available generators

| Generator | Writes to |
|---|---|
| `DefaultPathGenerator` | `disk/directory` |
| `DatePathGenerator` | `disk/directory/Y/m/d` |
| `UserPathGenerator` | `disk/directory/{user-auth-identifier}` |

## Setting one

Globally, through the `path_generator` config key:

```php
use Awcodes\Curator\PathGenerators\DatePathGenerator;

'path_generator' => DatePathGenerator::class,
```

Or per field:

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\PathGenerators\DatePathGenerator;

CuratorPicker::make('image')
    ->pathGenerator(DatePathGenerator::class);
```

## Writing your own

Implement the `PathGenerator` contract:

```php
use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        return ($baseDir ? $baseDir . '/' : '') . 'my/custom/path';
    }
}
```

`$baseDir` is the configured directory, so honour it unless you deliberately want to ignore it.
