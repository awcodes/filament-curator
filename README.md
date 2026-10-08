![curator-og](https://res.cloudinary.com/aw-codes/image/upload/w_1200,f_auto,q_auto/plugins/curator/awcodes-curator.jpg)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/awcodes/filament-curator.svg?style=flat-square)](https://packagist.org/packages/awcodes/filament-curator)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/filament-curator.svg?style=flat-square)](https://packagist.org/packages/awcodes/filament-curator)

# Filament Curator

A media picker/manager plugin for Filament Admin.

> [!WARNING]
> This package does not work with Spatie Media Library.

## Compatibility

| Package Version | Filament Version |
|-----------------|------------------|
| 1.x             | 2.x              |
| 2.x             | 2.x              |
| 3.x             | 3.x              |
| 4.x             | 4.x              |

## Upgrading from v3 to v4

Please see the [UPGRADE guide](UPGRADE.md) for instructions on upgrading from v3 to v4.

```bash
php artisan curator:upgrade
```

## Installation

You can install the package via composer then run the installation command:

```bash
composer require awcodes/filament-curator
```

```bash
php artisan curator:install
```

### Glide Token

The install command runs `php artisan curator:token` for you, which writes a freshly generated `CURATOR_GLIDE_TOKEN` into the `.env` file of the machine you ran it on. Curator uses it to sign the image URLs it renders, and the media route rejects any request whose signature doesn't match.

Because `.env` is usually gitignored, that token exists only where you installed. Every other environment — a teammate's checkout, your other machine, CI, staging, production — needs its own value, so add the key to your `.env.example` and set it wherever you deploy:

```dotenv
CURATOR_GLIDE_TOKEN=
```

The value does **not** have to match across environments. URLs are signed when they are rendered and validated by the same environment that served them, so each one can hold its own token. It does have to be present: with the variable missing, generating a media URL and serving the media route both fail.

Re-running `php artisan curator:token` overwrites an existing value rather than adding a second one. That invalidates any signed URL that has outlived the request it was rendered in — cached HTML, a CDN copy, an already-sent email, a URL pasted into stored content — which will start returning 403. If your config is cached, run `php artisan config:clear` after changing the token.

> [!NOTE]
> If you are using the stand-alone forms package then you will need to include the Curator modal in your layout file, typically you would place this, before the closing `body` tag.

```html
<x-curator::modals.modal />
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme add the plugin's views and styles to your theme css file or your app's css file if using the standalone packages.

```css
@import '../../../../vendor/awcodes/filament-curator/resources/css/plugin.css';

@source '../../../../vendor/awcodes/filament-curator/resources/**/*.blade.php';
```

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

Wildcards such as `text/*` or `application/*` never match HTML, JavaScript, XML (other than SVG) or the types above. To accept one of them, list it exactly.

> [!WARNING]
> Curator only sanitizes SVG uploads. Any other type you opt into is stored and served verbatim. Media served through Curator's route is sent with `X-Content-Type-Options: nosniff`, and restricted types are forced to `Content-Disposition: attachment`, but files on the `public` disk are also reachable directly through the `storage` symlink where those headers do not apply. If you allow executable types, serve them from a private disk.

#### How the stored extension is chosen

Curator detects an upload's type from the file's own contents, and decides whether to accept it from that type. The stored extension follows the same type, not the name the browser sent. Web servers pick a file's content type from its extension, so the two have to agree.

- The original extension is kept, lowercased, when it is a known extension for the detected type. `photo.jpeg` stays `.jpeg` and `PHOTO.JPG` becomes `.jpg`.
- Text content is often detected only as `text/plain`, so it may keep a plain-data extension such as `.csv`, `.md`, `.json`, `.yaml` or `.css`. Other text files are stored as `.txt`, including ones with uncommon extensions such as `.srt`. Text detected only as `text/plain` but named `.csv` or `.ics` gets the `text/csv` or `text/calendar` type, so it's typed the same whichever `libmagic` build PHP uses.
- An `.svg` file that starts with whitespace or a comment can be detected as plain text or XML. It is still stored as `.svg`, and sanitized, if its content parses as an SVG document.
- Office and OpenDocument files (`.docx`, `.xlsx`, `.pptx`, `.odt`, `.epub` and similar) are zip archives and are sometimes detected as `application/zip`. They keep their extension when the content is a zip archive. Legacy Office files (`.doc`, `.xls`, `.ppt` and their templates) are detected as an OLE container when they are large, and keep their extension when the content starts with the OLE signature.
- Otherwise the extension is replaced with the detected type's usual one. A JPEG uploaded as `photo.png` is stored as `.jpg`, and a file named `notes.html` whose contents are plain text is stored as `.txt`.
- When the detected type has no known extension, the file is stored as `.bin`, and Curator's route serves it as a download. This includes every `application/octet-stream` upload, if you have allowed that type.
- Extensions a server may run as code (`.php`, `.phtml`, `.phar`, `.shtml`, `.cgi` and similar) are never kept.

The type is read from the uploaded bytes by Curator itself. It doesn't rely on the type Livewire reports, because Livewire releases before 3.8.6 report the type the browser declared when the temporary upload disk is S3.

The same rule applies to `CuratorUtils::importMedia()`, which detects the type from the imported file's contents. `preserveFilenames()` affects only the base name; the extension still follows the detected type.

SVG markup is sanitized before it is stored. An upload detected as SVG that the sanitizer can't process, such as an XHTML document containing an `<svg>` element, is rejected and nothing is stored.

Only fresh uploads become media. Any other value in an upload field's state is rejected by validation.

#### Repairing media stored by earlier versions

Earlier versions kept the browser's extension. As a result, a database can hold media whose file extension doesn't match its contents, including some that a web server would serve as HTML. Versions 4.0.0 to 4.1.4 also accepted HTML files by default. After upgrading, check for these with a dry run:

```bash
php artisan curator:repair-extensions --dry-run
```

Then apply the changes:

```bash
php artisan curator:repair-extensions
```

For every media row, the command detects the stored file's type from its contents, then:

- **Renames files with an unsafe extension.** When the extension could be served as a document, script or server-side code (such as `.html`, `.svg`, `.xml`, `.js` or `.php`) but the content is something else, or the extension contains characters other than letters and digits, the file gets the extension for its detected type. Content detected as SVG is sanitized as it is renamed.
- **Neutralises HTML, XML and script content.** When the content itself is HTML, XHTML, XML or JavaScript, the file is renamed to `.txt`, so it is served as plain text. The bytes are kept for you to review or delete. If your global `acceptedFileTypes()` deliberately includes that type, the file is left in place and listed as `review` instead.
- **Reports harmless mismatches** and leaves them as they are, such as a JPEG stored as `.png`, or an extension that differs only in case, such as `.JPG`. Renaming them would only break existing links.

When a file is renamed, the row's `path`, `ext` and `type` are updated and the file stays in its directory.

- The file's contents are kept. Only SVG content is changed, by sanitizing. Each rename is printed as `old path -> new path`, so keep the output if you may need to undo a change.
- A row is skipped, with a warning, when its file is missing, when the new name is already taken, or when SVG content can't be sanitized.
- The media's URL changes with its path. Links to a renamed file that were copied elsewhere, for example into rich editor content, will no longer resolve.
- The command doesn't sanitize SVG files that already have an `.svg` extension. Run `php artisan curator:sanitize-svgs` for those.

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
    // see https://filamentphp.com/docs/4.x/forms/file-upload for more information about the following methods
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

The media panel takes these settings from the picker when it opens, and they can't be changed from the browser while it's open. Uploads go to the picker's `directory()`, or to an existing folder the user has browsed into. If you render the `curator-panel` Livewire component yourself, pass its configuration through the `settings` array when you mount it; setting its properties from the browser afterwards is rejected. When media is inserted, the panel sends the picker the stored records for the selected ids.

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
use Awcodes\Curator\View\Components\CuratorPicker;
use Awcodes\Curator\PathGenerators\DatePathGenerator;

public function register()
{
    CuratorPicker::make('image')
        ->pathGenerator(DatePathGenerator::class);
}
```

#### Available Generators

* `DefaultPathGenerator` will save files in disk/directory.
* `DatePathGenerator` will save files in disk/directory/Y/m/d.
* `UserPathGenerator` will save files in disk/directory/user-auth-identifier

You are also free to use your own Path Generators by implementing the
`PathGenerator` interface on your own classes.

```php
use Awcodes\Curator\PathGenerators;

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

#### Curation Limits

Saving a curation needs the `update` ability on the media, the same as editing it. With no policy registered for the media model, or a policy without an `update()` method, anyone who can reach the edit page can save one, as elsewhere in Filament. In Filament's strict authorization mode, a missing policy or `update()` method refuses the save instead.

A curation is saved at the size the crop is shown at in the cropper. A crop box can hang over the image's edge: the part of the image inside the box keeps its place and the overhang is padded, so nothing is stretched. A crop that misses the image entirely is rejected.

The size is capped by the `curation_max_dimension` config key, `8192` pixels by default. A curation whose longer side is bigger than that is scaled down to fit, keeping its shape. Set the key to `null` or `0` to turn the cap off. If you published the config before this option existed, add it there to change it.

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
value is fine. A fallback that ends up without a source can't be rendered
though, and referencing it from the blade component will throw.

Then you can reference your fallback in the blade component.

```blade
<x-curator-glider :media="1" fallback="thumbnail"/>
```

### Private Media

Glide URLs for public media are permanent. They never change for a given file and are served with a year-long public `Cache-Control`, so front-end pages and CDNs can cache them.

Media whose visibility isn't `public` gets temporary URLs instead, wherever Curator builds one: the model's `thumbnail_url`, `medium_url` and `large_url`, the Glider component and the Curator column. A temporary URL carries a signed expiry and the media's disk. Once it expires, or if either value is changed, the route refuses it with a 403. While it is valid, the response is sent with `Cache-Control: private` and a `max-age` that runs out with the URL, so shared caches don't keep it. A URL without an expiry only ever serves public media.

This also covers files Glide can't transform, such as PDFs, video and SVGs. Their size URLs stream the original file from the media's disk, so for private media they expire in the same way.

The lifetime is set in minutes by `temporary_url_expiration` in the config file, 5 by default. If you published the config before this option existed, add it there to change it. It also applies to the temporary disk URL the model's `url` returns for private media. Expiry times are rounded up to the minute, so pages that re-render keep the same URLs for a while.

Temporary URLs come out of a request for a page, so they don't belong anywhere they will be kept, such as cached HTML, a rich editor's content or an email. Build one in your own code with the model or the facade:

```php
use Awcodes\Curator\Facades\Glide;

$media->getGlideUrl(['w' => 800]); // temporary when the media isn't public

Glide::getTemporaryUrl($media->path, ['w' => 800], now()->addHour(), $media->disk);
```

`glide()->getUrl()` and `GlideBuilder::toUrl()` still build permanent URLs, so they only work for public media. A custom URL provider that builds its URLs with either of them gets temporary URLs for private media automatically. Pass private media to the Glider component as a `Media` instance or an id: a path given on its own builds a permanent URL.

The route checks the signature against the query string only, so parameters sent in a request body are never applied.

After upgrading:

- **Purge any CDN or proxy cache** in front of the media route. Copies of earlier permanent URLs for private media can stay in CDN and browser caches for up to a year.
- **Rotate `CURATOR_GLIDE_TOKEN`** (`php artisan curator:token`) if earlier URLs for private media may have been shared. This invalidates every URL issued before, public ones included, so pages and content that stored Glide URLs need them rebuilt.

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

public function register(): void 
{
    Glide::serverConfig([
        'driver' => 'imagick',
        'response' => new LaravelResponseFactory(app('request')),
        'source' => storage_path('app'),
        'source_path_prefix' => 'public',
        'cache' => storage_path('app'),
        'cache_path_prefix' => '.cache',
        'max_image_size' => 2000 * 2000,
    ]);
}
```

> [!IMPORTANT]
> **Using a cloud disk (S3, MinIO, etc.)?** The default config above points Glide's `source` at the local filesystem (`storage_path('app')` with `source_path_prefix => 'public'`). If your media lives on a cloud disk you **must** point the Glide `source` at that disk's Flysystem driver, otherwise Glide can't find the source images and they will fail to render.
>
> ```php
> use Awcodes\Curator\Facades\Glide;
> use Illuminate\Support\Facades\Storage;
>
> Glide::serverConfig([
>     'response' => new LaravelResponseFactory(app('request')),
>     'source' => Storage::disk('s3')->getDriver(),
>     'source_path_prefix' => '', // see note below
>     'cache' => Storage::disk('local')->getDriver(),
>     'cache_path_prefix' => '.cache',
>     'max_image_size' => 2000 * 2000,
> ]);
> ```
>
> A few things to watch for:
>
> - **`source_path_prefix`** must match where your objects actually live on the disk. Because a cloud disk's Flysystem is already rooted at the bucket (and your media `path` is stored relative to it), this is usually an empty string `''`. The `'public'` prefix in the default exists only because the local source is rooted at `storage_path('app')` while files live under `storage/app/public/`. A mismatched prefix is the most common cause of "images don't render" on cloud disks.
> - **Keep `cache` on a fast local disk.** Transformed images are cached there, so only the first request per variant reads the source from the cloud. A cold cache on a remote source is slow; a warm local cache is fast.
> - **Stray media on a different disk** (e.g. old records still on `public` while your source is S3) will fail source lookups and can slow things down — make sure existing records' `disk` matches your Glide source.
>
> - **Don't rely on `base_url`.** Curator always passes Glide the media's own path, so a `base_url` in your config is ignored.

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
@php
    $preset = new ThumbnailPreset();
@endphp

@if ($media->hasCuration('thumbnail'))
    <x-curator-curation :media="$media" curation="thumbnail"/>
@else
    <x-curator-glider
        class="object-cover w-auto"
        :media="$media"
        :width="$preset->getWidth()"
        :height="$preset->getHeight()"
    />
@endif
```

### Custom Model

If you want to use your own model for your media you can extend Curator's `Media` model with your own and set it in the config.

```php
use Awcodes\Curator\Models\Media;

class CustomMedia extends Media
{
    protected $table = 'media';
}
```

```php
'model' => \App\Models\Cms\Media::class,
```

## Testing

```bash
composer test
```

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
