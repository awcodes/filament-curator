# Filament Curator

A media picker and manager for Filament, with Glide-backed image transformations and per-image curations.

[![Latest Version](https://img.shields.io/github/release/awcodes/filament-curator.svg?style=flat-square&color=blue&label=Release)](https://github.com/awcodes/filament-curator/releases)
[![MIT Licensed](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/filament-curator.svg?style=flat-square&color=blue&label=Downloads)](https://packagist.org/packages/awcodes/filament-curator)
[![GitHub Repo stars](https://img.shields.io/github/stars/awcodes/filament-curator?style=flat-square&color=blue&label=Stars)](https://github.com/awcodes/filament-curator/stargazers)
[![Filament Version](https://img.shields.io/badge/Filament-4.x%20%26%205.x-d97706.svg?style=flat-square)](https://filamentphp.com/docs/5.x/panels/installation)

## Documentation

The full documentation lives at **[docs.aw.codes/curator](https://docs.aw.codes/curator/5.x)**.

## Compatibility

| Filament version | Package version |
|------------------|-----------------|
| 2.x              | 1.x             |
| 2.x              | 2.x             |
| 3.x              | 3.x             |
| 4.x              | 4.x             |
| 4.x & 5.x        | 5.x             |

## Installation

```bash
composer require awcodes/filament-curator
```

Then run `php artisan curator:install` to create the migration and Glide token and add Curator's styles to your Filament theme, as described in [Installation](https://docs.aw.codes/curator/5.x/installation), and on a panel [register the plugin](https://docs.aw.codes/curator/5.x/configuration).

## Changelog

Please see the [releases](https://github.com/awcodes/filament-curator/releases) for what has changed recently.

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
