# Filament Curator

A media picker plugin for Filament Admin.

:bangbang: This package is still in development

:bangbang: This package does not work with Spatie Media Library since it requires it's own media model.

![Gallery View](./images/gallery-view.png)

![Upload View](./images/upload-view.png)

![Button](./images/button.png)

## Installation

Install the package via composer.

```bash
composer require awcodes/filament-curator
```

Install Filament Curator into your app. This will publish the necessary migration and Filament resources.

```bash
php artisan curator:install
```

If you are using a custom Filament Admin Theme, be sure to add this package to your `tailwind.config.js` file.

```js
content: [
    ...
    "./vendor/awcodes/filament-curator/**/*.blade.php",
],
```

## Image Sizes

By default Curator will generate image sizes for each uploaded image based on the sizes setting in the config file. If you want to disable image sizes completely then set the sizes key to an empty array.

```php
'sizes' => [],
```

## Usage

Include the MediaPicker button in your forms to trigger the modal and either select an existing image or upload a new one.

```php
use FilamentCurator\Forms\MediaPicker;

MediaPicker::make(string $fieldName)
    ->label(string $customLabel)
```

Media can also be related to models by simply adding the relationship to your model.

```php
use FilamentCurator\Models\Media;

public function ogImage(): HasOne
{
    return $this->hasOne(Media::class, 'id', 'og_image');
}
```

To retrieve different sizes urls, Curator's Media model comes with a helper that takes in a size and returns the url for you. Sizes are based on your config settings.

If a size doesn't exist in your config, then it will return the full size image url.

```php
// Assuming a relationship on a Meta model for ogImage...

$meta->ogImage->getSizeUrl('thumbnail');
$meta->ogImage->getSizeUrl('medium');
$meta->ogImage->getSizeUrl('large');
```

If you need additional functionality you can extend Curator's Media model with your own.

```php
use FilamentCurator\Models\Media as CuratorMedia;

class Media extends CuratorMedia
{
    // ... custom methods and properties
}
```

## Versioning

This projects follow the [Semantic Versioning](https://semver.org/) guidelines.

## License

Copyright (c) 2022 Adam Weston and contributors

Licensed under the MIT license, see [LICENSE.md](LICENSE.md) for details.
