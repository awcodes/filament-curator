# Filament Curator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/awcodes/filament-curator.svg?style=flat-square)](https://packagist.org/packages/awcodes/filament-curator)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/filament-curator.svg?style=flat-square)](https://packagist.org/packages/awcodes/filament-curator)

A media picker/manager plugin for Filament Admin.

> **Warning**
> This package does not work with Spatie Media Library.

![curator-og](https://res.cloudinary.com/aw-codes/image/upload/w_1200,f_auto,q_auto/plugins/curator/awcodes-curator.jpg)

<!-- docs_start -->

## Installation

You can install the package via composer then run the installation command:

```bash
composer require awcodes/filament-curator
```

```bash
php artisan curator:install
```

***If you are using the stand-alone forms package then you will need to include the Curator modal in your layout file, typically you would place this, before the closing `body` tag.***

```html
<x-curator::modals.modal />
```

In an effort to align with Filament's theming methodology you will need to use a custom theme to use this plugin.

> **Note**
> If you have not set up a custom theme and are using a Panel follow the instructions in the [Filament Docs](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme) first. The following applies to both the Panels Package and the standalone Forms package.

You will also need to add cropper.js.

```bash
npm install -D cropperjs
```

1. Import the plugin's stylesheet and cropperjs' stylesheet into your theme's css file.

```css
@import '<path-to-node-modules>/cropperjs/dist/cropper.css';
@import '<path-to-vendor>/awcodes/filament-curator/resources/css/plugin.css';
```

2. Add the plugin's views to your `tailwind.config.js` file.

```js
content: [
    './vendor/awcodes/filament-curator/resources/**/*.blade.php',
]
```

## Upgrading

If you are upgrading from 2.x to 3.x you will also need to run:

```bash
php artisan curator:upgrade
```

This will update Curator's database schema and create a backup of your media table that can be deleted after upgrade
should you choose to do so.

### Additional Steps

1. `CurationPreset` will need to be updated to the [new format](#curation-presets).
2. `GliderFallback` will need to be updated to the [new format](#glider-fallback-images).
3. Custom Glide Server Factories will need to be updated to use the [new format](#custom-glide-server).

## Usage

### Global Settings

Global settings can be managed through the plugin's config file. You can publish the config file using the following:

```bash
php artisan vendor:publish --tag="curator-config"
```

### With Filament Panels

If you are using Filament Panels you will need to add the Plugin to your Panel's configuration. This will register the plugin's resources with the Panel. All methods are optional, and will be read from the config file if not provided.

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            \Awcodes\Curator\CuratorPlugin::make()
                ->label('Media')
                ->pluralLabel('Media')
                ->navigationIcon('heroicon-o-photo')
                ->navigationGroup('Content')
                ->navigationSort(3)
                ->navigationCountBadge()
                ->registerNavigation(false)
                ->defaultListView('grid' || 'list')
                ->resource(\App\Filament\Resources\CustomMediaResource::class)
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

CuratorPicker::make(string $fieldName)
    ->label(string $customLabel)
    ->buttonLabel(string | Htmlable | Closure $buttonLabel)
    ->color('primary|secondary|success|danger') // defaults to primary
    ->outlined(true|false) // defaults to true
    ->size('sm|md|lg') // defaults to md
    ->constrained(true|false) // defaults to false (forces image to fit inside the preview area)
    ->pathGenerator(DatePathGenerator::class|UserPathGenerator::class) // see path generators below
    ->lazyLoad(bool | Closure $condition) // defaults to true
    ->listDisplay(bool | Closure $condition) // defaults to true
    ->tenantAware(bool | Closure $condition) // defaults to true
    ->defaultPanelSort(string | Closure $direction) // defaults to 'desc'
    // see https://filamentphp.com/docs/2.x/forms/fields#file-upload for more information about the following methods
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

#### MorphAble

**Note:** The current implementation supports morphable relationships in forms, but it is not yet functional in table columns. Further adjustments are required for full compatibility.

Example Migration

```php
Schema::create('media_items', function (Blueprint $table) {
    $table->id();
    $table->morphs('mediable');
    $table->foreignId('media_id')->constrained()->onDelete('cascade');
    $table->integer('order');
    $table->string('type');
    $table->timestamps();
});
```

Model

```php
public function media(): MorphMany
{
    return $this->morphMany(MediaItem::class, 'mediable')->orderBy('order');
}
```

Form component

```php
CuratorPicker::make('document_ids')
    ->multiple()
    ->relationship('media', 'id')
    ->orderColumn('order') // Optional: Rename the order column if needed
    ->typeColumn('type') // Optional: Rename the type column if needed
    ->typeValue('document'); // Optional: Specify the type value if using types
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

`DefaultPathGenerator` will save files in disk/directory.

`DatePathGenerator` will save files in disk/directory/Y/m/d.

`UserPathGenerator` will save files in disk/directory/user-auth-identifier

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

If you are using a relationship to store your media then you will encounter n+1 issues on the column. In order to
prevent this you should modify your table query to eager load the relationship.

For example when using the admin panel in your ListResource

```php
protected function getTableQuery(): Builder
{
    return parent::getTableQuery()->with(['featured_image', 'product_pictures']);
}
```

### Curations

Curations are a way to create custom sizes and focal points for your images.

#### Curation Presets

If you have a curation that you are constantly using you can create Presets which will be available in the Curation
modal for easier reuse. After creating curation presets, they can be referenced by their key to output them in your
blade files.

```php
use Awcodes\Curator\Curations\CurationPreset;

class ThumbnailPreset extends CurationPreset
{
    public function getKey(): string
    {
        return 'thumbnail';
    }

    public function getLabel(): string
    {
        return 'Thumbnail';
    }

    public function getWidth(): int
    {
        return 200;
    }

    public function getHeight(): int
    {
        return 200;
    }

    public function getFormat(): string
    {
        return 'webp';
    }

    public function getQuality(): int
    {
        return 60;
    }
}
```

Then simply register your preset in the config.

```php
'curation_presets' => [
    ThumbnailPreset::class,
],
```

You can also change which formats are available for curations by changing the `curation_formats` in the config file. These should be compatible with Intervention Image's encoding types.

```php
'curation_formats' => [
    'jpg',
    'jpeg',
    'webp',
    'png',
    'avif',
],
```

If you wish to disable the "Curation" tab in the Media Editor you can do so by setting `tabs.display_curation`
to `false` in the config file. The default is `true`.

```php
'tabs' => [
    'display_curation' => false,
],
```

If you wish to disable the "Upload New" tab in the Media Editor you can do so by setting `tabs.display_upload_new`
to `false` in the config file. The default is `true`.

```php
'tabs' => [
    'display_upload_new' => false,
],
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
<div class="w-64 aspect-video">
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

class MyCustomGliderFallback extends GliderFallback
{
    public function getAlt(): string
    {
        return 'boring fallback image';
    }

    public function getHeight(): int
    {
        return 640;
    }

    public function getKey(): string
    {
        return 'card_fallback';
    }

    public function getSource(): string
    {
        return 'https://via.placeholder.com/640x420.jpg';
    }

    public function getType(): string
    {
        return 'image/jpg';
    }

    public function getWidth(): int
    {
        return 420;
    }
}
```

Then register your fallback in the config.

```php
'glide' => [
    'fallbacks' => [
        MyCustomGliderFallback::class,
    ],
],
```

Then you can reference your fallback in the blade component.

```blade
<x-curator-glider :media="1" fallback="card_fallback"/>
```

### Custom Glide Route

By default, Curator will use the route `curator` when serving images through Glide. If you want to change this you can update the `glide.route_path` setting in the Curator config file.

```php
'glide' => [
    'route_path' => 'uploads',
],
```

### Custom Glide Server

If you want to use your own Glide Server for handling served media with Glide you can implement the `ServerFactory` interface on your own classes and set it to the config.

```php
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;

class CustomServerFactory implements Contracts\ServerFactory
{
    public function getFactory(): ServerFactory | Server
    {
        return ServerFactory::create([
            'driver' => 'imagick',
            'response' => new LaravelResponseFactory(app('request')),
            'source' => storage_path('app'),
            'source_path_prefix' => 'public',
            'cache' => storage_path('app'),
            'cache_path_prefix' => '.cache',
            'max_image_size' => 2000 * 2000,
        ]);
    }
}
```

Then register your server in the config.

```php
'glide' => [
    'server' => \App\Glide\CustomServerFactory::class,
],
```

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

<!-- docs_end -->

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Adam Weston](https://github.com/awcodes)
- [The PHP League](https://glide.thephpleague.com/) for the awesome Glide package.
- [Cropperjs](https://github.com/fengyuanchen/cropperjs) for their amazing Javascript package.
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
