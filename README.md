# Filament Curator

A media picker/manager plugin for Filament Admin.

[![Latest Version](https://img.shields.io/github/release/awcodes/filament-curator.svg?style=flat-square&color=blue&label=Release)](https://github.com/awcodes/filament-curator/releases)
[![MIT Licensed](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/filament-curator.svg?style=flat-square&color=blue&label=Downloads)](https://packagist.org/packages/awcodes/filament-curator)
[![GitHub Repo stars](https://img.shields.io/github/stars/awcodes/filament-curator?style=flat-square&color=blue&label=Stars)](https://github.com/awcodes/filament-curator/stargazers)

> [!WARNING]
> This package does not work with Spatie Media Library.

## Compatibility

| Package Version | Filament Version |
|-----------------|------------------|
| 1.x             | 2.x              |
| 2.x             | 2.x              |
| 3.x             | 3.x              |
| 4.x             | 4.x              |
| 5.x             | 4.x & 5.x        |

<!-- [docs_start] -->

## Installation

You can install the package via composer then run the installation command:

```bash
composer require awcodes/filament-curator
```

```bash
php artisan curator:install
```

### What the install command does

`curator:install` asks a few questions and then writes the files Curator needs into your app:

1. **Prompts for UUID and tenancy support.** It asks whether the media table should use a UUID primary key, and whether Curator should be scoped to a tenant. If you answer yes to tenancy it also asks for the tenant model's name (for example `Team`).
2. **Creates the migration.** A timestamped `create_curator_table.php` is written to `database/migrations`. It creates the `curator` table with the file's storage columns (`disk`, `directory`, `visibility`, `name`, `path`, `ext`, `type`, `size`, `width`, `height`), its metadata columns (`alt`, `title`, `description`, `caption`, `pretty_name`, `exif`, `curations`), and a nullable foreign key for the tenant. The primary key is a UUID or an auto-incrementing ID depending on your answer, and the foreign key is named after your tenant model (`team_id`), or `tenant_id` when tenancy is off.
3. **Creates a Media model — only if you chose UUID or tenancy.** `app/Models/Media.php` extends `Awcodes\Curator\Models\Media`, adding Laravel's `HasUuids` trait and/or a `BelongsTo` relationship to your tenant model.
4. **Publishes and edits the config — also only in that case.** `config/curator.php` is published and pointed at your new model, and the `tenancy` block is switched on with your relationship name. If you skip both options, no config file is published; publish it yourself later with `php artisan vendor:publish --tag="curator-config"` when you want to change the defaults.
5. **Generates the Glide token.** It calls `php artisan curator:token`, which writes `CURATOR_GLIDE_TOKEN` into your `.env`. See [Glide Token](#glide-token) below — this one needs attention beyond your local machine.
6. **Offers to run the migrations.** Answer yes and it runs `php artisan migrate` for you; answer no and the migration sits in `database/migrations` until you run it.

The prompts can be answered up front if you want the command to run unattended — each option takes a value, so pass `--use-uuid=1`, `--tenancy-name=Team` and `--run-migrations=1` (use `0` to opt out).

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels follow the instructions in the [Filament Docs](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme add the plugin's views and styles to your theme css file or your app's css file if using the standalone packages.

```css
@import '../../../../vendor/awcodes/filament-curator/resources/css/plugin.css';

@source '../../../../vendor/awcodes/filament-curator/resources/**/*.blade.php';
```

> [!NOTE]
> If you are using the stand-alone forms package then you will need to include the Curator modal in your layout file, typically you would place this, before the closing `body` tag.

```html
<x-curator::modals.modal />
```

### Glide Token

The install command runs `php artisan curator:token` for you, which writes a freshly generated `CURATOR_GLIDE_TOKEN` into the `.env` file of the machine you ran it on. Curator uses it to sign the image URLs it renders, and the media route rejects any request whose signature doesn't match.

Because `.env` is usually gitignored, that token exists only where you installed. Every other environment — a teammate's checkout, your other machine, CI, staging, production — needs its own value, so add the key to your `.env.example` and set it wherever you deploy:

```dotenv
CURATOR_GLIDE_TOKEN=
```

The value does **not** have to match across environments. URLs are signed when they are rendered and validated by the same environment that served them, so each one can hold its own token. It does have to be present: with the variable missing, generating a media URL and serving the media route both fail.

Re-running `php artisan curator:token` overwrites an existing value rather than adding a second one. That invalidates any signed URL that has outlived the request it was rendered in — cached HTML, a CDN copy, an already-sent email, a URL pasted into stored content — which will start returning 403. If your config is cached, run `php artisan config:clear` after changing the token.

## Usage

### Global Settings

Global settings can be managed through the plugin's config file. You can publish the config file using the following:

```bash
php artisan vendor:publish --tag="curator-config"
```

### Accepted File Types

When you don't set `acceptedFileTypes()` yourself, Curator falls back to `MimeType::defaults()` — the full `MimeType` list minus types that are effectively executable content:

`text/html`, `application/xhtml+xml`, `text/javascript`, `application/xml`, `application/vnd.mozilla.xul+xml`, `application/x-httpd-php`, `application/x-sh`, `application/x-csh`, `application/x-shockwave-flash` and `application/octet-stream`.

These are excluded because uploaded files are served from your application's own origin. An HTML or XML document containing a `<script>` tag executes with the session of whoever opens it, and with the default `public` disk the storage directory sits inside the document root, where a server may execute scripts directly. `application/octet-stream` is excluded separately: it's what `finfo` reports for anything it can't classify, so allowing it turns the allow list into a wildcard.

You can still opt back in, per field or globally, if your application genuinely needs to host these:

```php
use Awcodes\Curator\Enums\MimeType;

// globally
Curator::acceptedFileTypes([...MimeType::defaults(), 'text/html']);

// or per field
CuratorPicker::make('attachment')
    ->acceptedFileTypes([...MimeType::defaults(), 'text/html']);
```

> [!WARNING]
> Curator only sanitizes SVG uploads. Any other type you opt into is stored and served verbatim. Media served through Curator's route is sent with `X-Content-Type-Options: nosniff`, and restricted types are forced to `Content-Disposition: attachment`, but files on the `public` disk are also reachable directly through the `storage` symlink where those headers do not apply. If you allow executable types, serve them from a private disk.

### With Filament Panels

If you are using Filament Panels you will need to add the Plugin to you Panel's configuration. This will register the plugin's resources with the Panel. All methods are optional, and will be read from the config file if not provided.

```php
use Awcodes\Curator\CuratorPlugin;
use Filament\Support\Icons\Heroicon

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            CuratorPlugin::make()
                ->label('Media')
                ->pluralLabel('Media')
                ->navigationIcon(Heroicon::OutlinedPhoto)
                ->navigationGroup('Content')
                ->navigationSort(3)
                ->showBadge(true) 
                ->registerNavigation(true)
                ->curations(true)
                ->fileSwap(true),  
        ]);
}
```

### Curator Picker Field

Include the CuratorPicker field in your forms to trigger the modal and either
select an existing image or upload a new one. Some common methods
from Filament's `FileUpload` component can be used to help with sizing,
validation, etc. for specific instances of each CuratorPicker.

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Support\Enums\Size;

CuratorPicker::make(string $fieldName)
    ->label(string $customLabel)
    ->buttonLabel(string | Htmlable | Closure $buttonLabel)
    ->color('primary|secondary|success|danger') // defaults to gray
    ->outlined(true|false) // defaults to true
    ->size(Size::Medium)
    ->constrained(true|false) // defaults to false (forces image to fit inside the preview area)
    ->pathGenerator(DatePathGenerator::class|UserPathGenerator::class) // see path generators below
    ->lazyLoad(bool | Closure $condition) // defaults to true
    ->listDisplay(bool | Closure $condition) // defaults to true
    ->tenantAware(bool | Closure $condition) // defaults to true
    ->defaultPanelSort(string | Closure $direction) // defaults to 'desc'
    // see https://filamentphp.com/docs/5.x/forms/file-upload for more information about the following methods
    ->preserveFilenames()
    ->maxWidth()
    ->minSize()
    ->maxSize()
    ->rules()
    ->acceptedFileTypes()
    ->disk()
    ->visibility()
    ->directory()
    ->imageCropAspectRatio()
    ->imageResizeTargetWidth()
    ->imageResizeTargetHeight()
    ->multiple() // required if using a relationship with multiple media
    ->relationship(string $relationshipName, string 'titleColumnName')
    ->orderColumn('order') // only necessary to rename the order column if using a relationship with multiple media
```

### Relationships

#### Single

Form component

```php
CuratorPicker::make('featured_image_id')
    ->relationship('featured_image', 'id'),
```

Model

```php
use Awcodes\Curator\Models\Media;

public function featuredImage(): BelongsTo
{
    return $this->belongsTo(Media::class, 'featured_image_id', 'id');
}
```

#### Multiple

Form component

```php
CuratorPicker::make('product_picture_ids')
    ->multiple()
    ->relationship('product_pictures', 'id')
    ->orderColumn('order'), // only necessary if you need to rename the order column
```

Model

```php
use Awcodes\Curator\Models\Media;

public function productPictures(): BelongsToMany
{
    return $this
        ->belongsToMany(Media::class, 'media_post', 'post_id', 'media_id')
        ->withPivot('order')
        ->orderBy('order');
}
```

### RichEditor Integration

Curator comes with built-in integration for Filament's RichEditor field.

```php
use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;

\Filament\Forms\Components\RichEditor::make('content')
    ->tools([
        'attachCuratorMedia'
    ])
    ->plugins([
        AttachCuratorMediaPlugin::make(),
    ]),
```

### Path Generation

By default, Curator will use the directory and disk set in the config to
store your media. If you'd like to store the media in a different way
Curator comes with Path Generators that can be used to modify the behavior.
Just set the one you want to use globally in the config or per instance on your `CuratorPicker` field.

```php
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\PathGenerators\DatePathGenerator;

CuratorPicker::make('image')
    ->pathGenerator(DatePathGenerator::class);
```

#### Available Generators

* `DefaultPathGenerator` will save files in disk/directory.
* `DatePathGenerator` will save files in disk/directory/Y/m/d.
* `UserPathGenerator` will save files in disk/directory/user-auth-identifier

You are also free to use your own Path Generators by implementing the
`PathGenerator` interface on your own classes.

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

### Curator Column

To render your media in a table Curator comes with a `CuratorColumn` which has the same methods as Filament's
ImageColumn.

```php
CuratorColumn::make('featured_image')
    ->size(40)
```

For multiple images you can control the number of images shown, the ring size and the overlap.

```php
CuratorColumn::make('product_pictures')
    ->ring(2) // options 0,1,2,4
    ->overlap(4) // options 0,2,3,4
    ->limit(3),
```

#### Relationships

If you are using a relationship to store your media then you will encounter n+1 issues on the column. In order to prevent this you should modify your table query to eager load the relationship.

For example when using the admin panel in your ListResource

```php
protected function getTableQuery(): Builder
{
    return parent::getTableQuery()->with(['featured_image', 'product_pictures']);
}
```

Or, if you are using a Table class

```php
public static function configure(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->with('media', 'gallery'));
}
```

### Curations

Curations are a way to create custom sizes and focal points for your images.

#### Curation Presets

If you have a curation that you are constantly using you can create Presets which will be available in the Curation modal for easier reuse. After creating curation presets, they can be referenced by their key to output them in your blade files.

```php
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Facades\Curation;

public function register(): void
{
    Curation::presets([
        CurationPreset::make('Thumbnail')
            ->height(200)
            ->format('webp')
            ->quality(80)
            ->width(200)
    ]);
}
```

### Glider Blade Component

To make it as easy as possible to output your media, Curator comes with an
`<x-curator-glider>` blade component.

See [Glide's quick reference](https://glide.thephpleague.com/2.0/api/quick-reference/) for more information about
Glide's options.

**Special attributes**

- media: id (int) or model (Media) instance ***required***
- loading: defaults to 'lazy'
- glide: this can be used to pass in a glide query string if you do not want to use individual attributes
- srcset: this will output the necessary srcset with glide generated urls.
  Must be an array of srcset widths and requires the 'sizes' attribute to
  also be set.
- force: (bool) this can be used to force glider to return a signed url and is helpful when returning urls from cloud disks. This should be used with the knowledge that it could have performance implications.

```blade
<div class="aspect-video w-64">
    <x-curator-glider
        class="object-cover w-auto"
        :media="1"
        glide=""
        fallback=""
        :srcset="['1024w','640w']"
        sizes="(max-width: 1200px) 100vw, 1024px"
        background=""
        blur=""
        border=""
        brightness=""
        contrast=""
        crop=""
        device-pixel-ratio=""
        filter=""
        fit=""
        flip=""
        format=""
        gamma=""
        height=""
        quality=""
        orientation=""
        pixelate=""
        sharpen=""
        width=""
        watermark-path=""
        watermark-width=""
        watermark-height=""
        watermark-x-offset=""
        watermark-y-offset=""
        watermark-padding=""
        watermark-position=""
        watermark-alpha=""
    />
</div>
```

#### Glider Fallback Images

Glider allows for a fallback image to be used if the media item does not
exist. This can be set by passing in the `fallback` attribute referencing
one of your registered `GliderFallback`s.

```php
use Awcodes\Curator\Glide\GliderFallback;
use Awcodes\Curator\Facades\Glide;

public function register(): void
{
    Glide::registerGliderFallbacks([
        GliderFallback::make('thumbnail')
            ->alt(?string)
            ->height(?int)
            ->source(?string)
            ->type(?string)
            ->width(?int),
    ]);
}
```

Everything except the name is optional and may be null, so a conditional
value is fine. Referencing a fallback that was never registered, or one that
ends up without a source, is a configuration mistake, so the blade component
throws.

Without a fallback, a media item that can't be found (a null value, or an id
whose record has been deleted) renders nothing, and an id that no longer
resolves is logged as a warning.

Then you can reference your fallback in the blade component.

```blade
<x-curator-glider :media="1" fallback="thumbnail"/>
```

### Custom Glide Route

By default, Curator will use the route `curator` when serving images through Glide. If you want to change this you can update the `basePath` in a service provider.

```php
use Awcodes\Curator\Facades\Glide;

public function register(): void 
{
    Glide::basePath('media');
}
```

### Custom Glide Server

If you want to use your own Glide Server for handling served media with Glide you can pass the server config to the Glide facade in a service provider.

```php
use Awcodes\Curator\Facades\Glide;
use Illuminate\Support\Facades\Storage;

public function register(): void 
{
    Glide::serverConfig([
        'driver' => 'imagick',
        'response' => new LaravelResponseFactory(app('request')),
        'source' => Storage::disk('public')->getDriver(),
        'cache' => storage_path('app'),
        'cache_path_prefix' => '.cache',
        'max_image_size' => 2000 * 2000,
    ]);
}
```

> [!NOTE]
> **Using a cloud disk (S3, MinIO, etc.)?** No custom server is needed. Out of the box, Glide reads each media item from the disk it was stored on, through that disk's Flysystem driver, and caches transformed images locally in `storage/app/.cache`. A config you pass to `Glide::serverConfig()` replaces that default completely. So if you supply one, its `source` has to point at the disk your media actually lives on, for example `'source' => Storage::disk('s3')->getDriver()`. Because Curator stores `path` relative to that disk, `source_path_prefix` should normally be left out or set to `''`.

### Curation Blade Component

To make it as easy as possible to output your curations, Curator comes with an
`<x-curator-curation>` blade component.

**Special attributes**

- media: id (int) or model (Media) instance ***required***

```blade
<x-curator-curation :media="10" curation="thumbnail" loading="lazy"/>
```

### Practical use case

Since curations may or may not exist for each media item it's good to use a fallback to the glider component in your
blade file so images always get rendered appropriately. This also keeps you from having to create curations for every
media item, only the ones where you're trying to change the focal point, etc.

```blade
@if ($media->hasCuration('thumbnail'))
    <x-curator-curation :media="$media" curation="thumbnail"/>
@else
    <x-curator-glider
        class="object-cover w-auto"
        :media="$media"
        width="200"
        height="200"
    />
@endif
```

Keep the fallback's dimensions in step with the preset's. If you'd rather not repeat them, `Curation::getPresets()` returns the registered `CurationPreset` objects, which expose `getKey()`, `getWidth()`, `getHeight()`, `getFormat()` and `getQuality()`.

### Custom Model

If you want to use your own model for your media you can extend Curator's `Media` model with your own and set it in the config.

```php
namespace App\Models;

use Awcodes\Curator\Models\Media as CuratorMedia;

class Media extends CuratorMedia
{
    //
}
```

```php
'model' => \App\Models\Media::class,
```

There's no need to set `$table` — the parent already points at `curator`. Only declare it if you've actually renamed the table.

### Customising delete actions

Media can be deleted from three places: the resource table (row and bulk actions), the edit page, and the picker panel. `Curator::configureDeleteActionsUsing()` hooks all of them at once, so you can add a warning, a confirmation step, or a guard without touching Filament's global `DeleteAction::configureUsing()`. Call it from a service provider's `boot()` method:

```php
use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Illuminate\Support\Collection;

Curator::configureDeleteActionsUsing(function (Action $action): void {
    if ($action instanceof BulkAction) {
        $action->modalDescription(fn (Collection $records): string => "Delete {$records->count()} files? Any content using them will lose its image.");

        return;
    }

    $action->modalDescription(fn (?Media $record): string => "Delete {$record?->name}? Any content using it will lose its image.");
});
```

The callback receives the action itself, so any Filament action method is available, including `before()` with `$action->halt()` to stop a deletion. Single-record actions receive the media as `$record` and the bulk action receives `$records`. Type `$record` as nullable: the picker panel only resolves it once the action is opened, and not at all if the user may not delete that item.

The hook doesn't change how deletes are authorized: the resource actions still check your media policy, and the picker panel still checks it again before deleting. Your callback can override that, though. Calling `authorize()` replaces the resource's policy check, and calling `action()` on the picker panel's action replaces its checked delete logic. Avoid both unless you intend to take over authorization.

If you have replaced the resource's page or table classes in the config, your versions need to pass their delete actions through `Curator::configureDeleteAction()` to pick up the hook.

<!-- [docs_end] -->

## Testing

```bash
composer test
```

## Development Workbench

Install the PHP dependencies, build the consuming application's assets and
database, then serve the Workbench:

```bash
composer install
composer build
composer serve
```

The Filament panel is available at <http://127.0.0.1:8000/admin>. Sign in with
`test@example.com` and `password`. The Workbench uses Curator's documented
plugin, installer, and public-disk configuration; Node.js is required to compile
the consumer-owned Filament theme.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Adam Weston](https://github.com/awcodes)
- [The PHP League](https://glide.thephpleague.com/) for the awesome Glide package.
- [Cropperjs](https://github.com/fengyuanchen/cropperjs) for their amazing Javascript package.
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
