# Filament Curator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/awcodes/filament-curator.svg?style=flat-square)](https://packagist.org/packages/awcodes/filament-curator)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/filament-curator.svg?style=flat-square)](https://packagist.org/packages/awcodes/filament-curator)

A media picker/manager plugin for Filament Admin.

> **Warning**
> This package does not work with Spatie Media Library.

> **Warning**
> If you are using the Curator integration with Filament Tiptap Editor you will also need to update it to version 2.3.0 or higher.

![curator-og](https://user-images.githubusercontent.com/3596800/225419661-a0431c1b-957d-466f-a94d-a73a40b11d72.png)

## Installation

You can install the package via composer then run the installation command:

```bash
composer require awcodes/filament-curator
php artisan curator:install
```

## Upgrading

If you are upgrading from 1.x to 2.x you will also need to run:

```bash
php artisan curator:upgrade
```

This will update Curator's database schema and create a backup of your media table that can be deleted after upgrade should you choose to do so.

### Additional Steps
1. Change any references in your codebase from `$media->filename` to `$media->path`.
2. Change any use statements from `FilamentCurator` to `RocketFirm\Curator`.
3. Change `FilamentCurator\Forms\Components\MediaPicker` fields to
   `RocketFirm\Curator\Components\Forms\CuratorPicker`.

## Usage

### Global Settings

Global settings for Curator are handled through the `Curator` facade.
Inside the `register()` method of a service provider you can customize the
behaviour of Curator's resources. All methods are optional.

```php
use RocketFirm\Curator\Facades\Curator;

public function register()
{
    Curator::acceptedFileTypes(array|Closure $types)
        ->cloudDisks(array $disks)
        ->curationPresets(array|null $presets)
        ->directory(Closure|string|null $directory)
        ->disableResourceRegistration()
        ->disk(string|Closure|null $disk)
        ->glideCachePathPrefix(string $prefix)
        ->glideDriver(string $driver)
        ->gliderFallbacks(array|null $fallbacks)
        ->glideMaxImageSize(int $size)
        ->glideServer(Server|ServerFactory|null $server)
        ->glideSourcePathPrefix(string $prefix)
        ->imageCropAspectRatio(string|Closure|null $ratio)
        ->imageResizeTargetHeight(string|Closure|null $height)
        ->imageResizeTargetWidth(string|Closure|null $width)
        ->limitToDirectory(bool|Closure|null $condition = false)
        ->maxSize(int|Closure $size)
        ->maxWidth(int|Closure $width)
        ->mediaModel(string $model)
        ->minSize(int|Closure $size)
        ->navigationGroup(string $group)
        ->navigationIcon(string $label)
        ->navigationSort(int $order)
        ->pathGenerator(PathGenerator|string|null $generator)
        ->pluralResourceLabel(string|Closure $label)
        ->preserveFilenames(bool|Closure $condition)
        ->registerNavigation(bool|Closure|null $condition)
        ->resourceLabel(string|Closure $label)
        ->tableHasGridLayout(bool|Closure|null $condition)
        ->tableHasIconActions(bool|Closure|null $condition)
        ->visibility(string|Closure|null $visibility)
}
```

### Curator Picker Field

Include the CuratorPicker field in your forms to trigger the modal and either
select an existing image or upload a new one. Some common methods
from Filament's `FileUpload` component can be used to help with sizing,
validation, etc. for specific instances of each CuratorPicker.

```php
use RocketFirm\Curator\Components\Forms\CuratorPicker;

CuratorPicker::make(string $fieldName)
    ->label(string $customLabel)
    ->buttonLabel(string | Htmlable | Closure $buttonLabel)
    ->color('primary|secondary|success|danger') // defaults to primary
    ->outlined(true|false) // defaults to true
    ->size('sm|md|lg') // defaults to md
    ->constrained(true|false) // defaults to false (forces image to fit inside 
    the preview area)
    ->pathGenerator(DatePathGenerator::class|UserPathGenerator::class) // see path generators below
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
use RocketFirm\Curator\Models\Media;

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
use RocketFirm\Curator\Models\Media;

public function productPictures(): BelongsTo
{
    return $this
        ->belongsToMany(Media::class, 'media_post', 'post_id', 'media_id')
        ->withPivot('order')
        ->orderBy('order');
}
```

### Path Generation

By default, Curator will use the directory and disk set in the config to
store your media. If you'd like to store the media in a different way
Curator comes with Path Generators that can be used to modify the behavior.
Just set the one you want to use the `register()` method of a service provider.

```php
public function register()
{
    Curator::pathGenerator(DatePathGenerator::class);
}
```

#### Available Generators

`DefaultPathGenerator` will save files in disk/directory.

`DatePathGenerator` will save files in disk/directory/Y/m/d.

`UserPathGenerator` will save files in disk/directory/user-auth-identifier

You are also free to use your own Path Generators by implementing the
`PathGenerator` interface on your own classes.

```php
use RocketFirm\Curator\Generators;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        return ($baseDir ? $baseDir . '/' : '') . 'my/custom/path';
    }
}
```

Path Generators can also be passed into the `directory()` method on the
`CuratorPicker` field for per instance use.

```php
CuratorPicker::make(string $fieldName)
    ->label(string $customLabel)
    ->pathGenerator(CustomPathGenerator::class),
```

### Curator Column

To render your media in a table Curator comes with a `CuratorColumn` which has the same methods as Filament's ImageColumn.

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

### Curations

Curations are a way to create custom sizes and focal points for your images. 


#### Curation Presets
If you have a curation that you are constantly using you can create Presets which will be available in the Curation modal for easier reuse. After creating curation presets, they can be referenced by their key to output them in your blade files.

```php
use RocketFirm\Curator\CurationPreset;

Curator::curationPresets([
    CurationPreset::make('thumbnail')
        ->label('Thumbnail')
        ->width(200)
        ->height(200)
        ->format('webp')
        ->quality(80),
    CurationPreset::make('hero')
        ->label('Hero')
        ->width(960)
        ->height(300),
    CurationPreset::make(name: 'og-image')
        ->label('OG Image')
        ->width(1200)
        ->height(630),
]);
```

### Glider Blade Component

To make it as easy as possible to output your media, Curator comes with an
`<x-curator-glider>` blade component.

See [Glide's quick reference](https://glide.thephpleague.com/2.0/api/quick-reference/) for more information about Glide's options.

**Special attributes**

- media: id (int) or model (Media) instance ***required***
- loading: defaults to 'lazy'
- glide: this can be used to pass in a glide query string if you do not want to use individual attributes
- srcset: this will output the necessary srcset with glide generated urls.
  Must be an array of srcset widths and requires the 'sizes' attribute to
  also be set.

```html
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

#### Fallback Images

Glider allows for a fallback image to be used if the media item does not 
exist. This can be set by passing in the `fallback` attribute referencing 
one of your registered `GliderFallback`s.

```php
use RocketFirm\Curator\GliderFallback;

Curator::gliderFallbacks([
    GliderFallback::make(key: 'thumbnail')
        ->source('defaults/thumbnail.jpg')
        ->alt('party at LaraconIN')
        ->width(200)
        ->height(200),
]);
```

```html
<x-curator-glider :media="1" fallback="thumbnail" />
```

### Curation Blade Component

To make it as easy as possible to output your curations, Curator comes with an
`<x-curator-curation>` blade component.

**Special attributes**

- media: id (int) or model (Media) instance ***required***

```html
<x-curator-curation :media="10" curation="thumbnail" loading="lazy" />
```

### Practical use case

Since curations may or may not exist for each media item it's good to use a fallback to the glider component in your blade file so images always get rendered appropriately. This also keeps you from having to create curations for every media item, only the ones where you're trying to change the focal point, etc.

```html
@if ($media->hasCuration('thumbnail'))
    <x-curator-curation :media="$media" curation="thumbnail" />
@else
    <x-curator-glider
        class="object-cover w-auto"
        :media="$media"
        :width="curator()->preset('thumbnail')['width']"
        :height="curator()->preset('thumbnail')['height']"
    />
@endif
```

### Custom Resource

Should you need to override the default Resources, it is recommended
that you use the service container to bind Curator's Resource name to your own extensions of them.

```php
use RocketFirm\Curator\Resources\MediaResource;
use RocketFirm\Curator\Facades\Curator;

class YourNotAsCoolMediaResource extends MediaResource
{
    // ... custom methods and properties
}

class YourNotAsCoolEditMedia extends MediaResource\EditMedia
{
    // ... custom methods and properties
}

class YourNotAsCoolCreateMedia extends MediaResource\CreateMedia
{
    // ... custom methods and properties
}

class YourNotAsCoolListMedia extends MediaResource\ListMedia
{
    // ... custom methods and properties
}

// and in a service provider
public function register()
{
    Curator::disableResourceRegistration();
    $this->app->bind(MediaResource::class, fn() => new YourNotAsCoolMediaResource());
    $this->app->bind(MediaResource\EditMedia::class, fn() => new YourNotAsCoolEditMedia());
    $this->app->bind(MediaResource\CreateMedia::class, fn() => new YourNotAsCoolCreateMedia());
    $this->app->bind(MediaResource\ListMedia::class, fn() => new YourNotAsCoolListMedia());
}
```

## Theming

If you are using a custom theme for Filament you will need to add this plugin's
views to your `tailwind.config.js`.

```js
content: [
    ...
    "./vendor/rocket-firm/curator/resources/views/**/*.blade.php",
]
```

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
