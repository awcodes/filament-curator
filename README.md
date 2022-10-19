# Filament Curator

A media picker plugin for Filament Admin.

:bangbang: This package is still in development

:bangbang: This package does not work with Spatie Media Library since it
requires it's own media model.

![Gallery View](./images/curator-gallery-dark.jpg)

![Upload View](./images/curator-upload-dark.jpg)

![Field View](./images/curator-media-picker-dark.jpg)

## Installation

Install the package via composer.

```bash
composer require awcodes/filament-curator
```

(optional) Publish the config.

```bash
php artisan vendor:publish --tag=filament-curator-config
```

Install Filament Curator into your app. This will publish the necessary
migration and Filament resources.

```bash
php artisan curator:install
```

## Image Sizes

By default Curator will generate image sizes for each uploaded image based on
the sizes setting in the config file. If you want to disable image sizes
completely then set the sizes key to an empty array. 

***NOTE: In an effort to keep 
tables performant, Curator will still generate thumbnails even if this 
options is set to an empty array.***

```php
'sizes' => [],
```

### Regenerating Image Sizes

If you need to regenerate all of your image sizes, for instance you add or
remove a size from your config you can do so with the 
`curator:regenerate-thumbnails` command.

```bash
php artisan curator:regenerate-thumbnails --chunk=100
```

## Cloud Providers Table Display

By default in the Media Resource table the disk icon is set to display a cloud
if the disk is either 'Cloudinary' or 's3'. If you would like to extend or
change this you can do so in the `filament-curator.php` config file.

```php
'cloud_disks' => ['cloudinary', 's3', 'your_cloud_provider'];
```

## Usage

Include the MediaPicker button in your forms to trigger the modal and either
select an existing image or upload a new one.

```php
use FilamentCurator\Forms\Components\MediaPicker;

MediaPicker::make(string $fieldName)
    ->label(string $customLabel)
    ->buttonLabel(string | Htmlable | Closure $buttonLabel)
    ->color('primary|secondary|success|danger') // defaults to primary
    ->outlined(true|false) // defaults to true
    ->size('sm|md|lg') // defaults to md
    ->fitContent(true|false) // defaults to false (forces image to fit inside the preview area)
```

Media can also be related to models by simply adding the relationship to your
model.

```php
use FilamentCurator\Models\Media;

public function featuredImage(): HasOne
{
    return $this->hasOne(Media::class, 'id', 'featured_image');
}
```

To retrieve different sizes urls, Curator's Media model comes with a helper that
takes in a size and returns the url for you. Sizes are based on your config
settings.

If a size doesn't exist in your config, then it will return the full size image
url.

```php
// Assuming a relationship on a Post model for featuredImage...

$post->featuredImage->getSizeUrl('thumbnail');
$post->featuredImage->getSizeUrl('medium');
$post->featuredImage->getSizeUrl('large');
```

## Table Display

To show an item in your tables you can use the ThumbnailColumn. This column
extends Filament's ImageColumn and can use the same methods available on that
column. By default all image columns will show a generic document image 
unless their mime type is an 'image'.

***NOTE: CuratorColumn is deprecated. Please use ThumbnailColumn instead.***

```php
// Deprecated
CuratorColumn::make('relationship_name')

ThumbnailColumn::make('relationship.name')
    ->additionalMethodsFromFilamentImageColumn()
```

## Custom Media Model

If you need additional functionality you can extend Curator's Media model with
your own by updating the 'model' setting in the config file with your own model.

```php
use FilamentCurator\Models\Media as CuratorMedia;

class Media extends CuratorMedia
{
    // ... custom methods and properties
}
```

## Themeing

If you are using a custom theme for Filament you will need to add this plugin's
views to your Tailwind CSS config.

```js
content: [
    ...
        "./vendor/awcodes/filament-curator/resources/views/**/*.blade.php",
],
```

## Versioning

This projects follow the [Semantic Versioning](https://semver.org/) guidelines.

## License

Copyright (c) 2022 Adam Weston and contributors

Licensed under the MIT license, see [LICENSE.md](LICENSE.md) for details.
