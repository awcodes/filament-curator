---
title: Installation
description: Install Curator, run its installer, and register its styles with your Filament theme.
---

# Installation

## Requiring the package

Install via Composer, then run the installer:

```bash
composer require awcodes/filament-curator
```

```bash
php artisan curator:install
```

## What the install command does

`curator:install` asks a few questions and then writes the files Curator needs into your app.

1. **Prompts for UUID and tenancy support.** It asks whether the media table should use a UUID primary key, and whether Curator should be scoped to a tenant. Answer yes to tenancy and it also asks for the tenant model's name, for example `Team`.
2. **Creates the migration.** A timestamped `create_curator_table.php` is written to `database/migrations`, creating the `curator` table with the file's storage columns (`disk`, `directory`, `visibility`, `name`, `path`, `ext`, `type`, `size`, `width`, `height`), its metadata columns (`alt`, `title`, `description`, `caption`, `pretty_name`, `exif`, `curations`), and a nullable tenant foreign key. The primary key is a UUID or an auto-incrementing ID depending on your answer, and the foreign key is named after your tenant model (`team_id`), or `tenant_id` when tenancy is off.
3. **Creates a Media model — only if you chose UUID or tenancy.** `app/Models/Media.php` extends `Awcodes\Curator\Models\Media`, adding Laravel's `HasUuids` trait and/or a `BelongsTo` relationship to your tenant model.
4. **Publishes and edits the config — also only in that case.** `config/curator.php` is published, pointed at your new model, and its `tenancy` block switched on with your relationship name. If you skip both options no config file is published; publish it yourself later when you want to change the defaults. See [Configuration](configuration.md).
5. **Generates the Glide token.** It runs `php artisan curator:token`, writing `CURATOR_GLIDE_TOKEN` into your `.env`. This one needs attention beyond your own machine — see [Glide token](storage/token.md).
6. **Offers to run the migrations.** Answer yes and it runs `php artisan migrate`; answer no and the migration waits in `database/migrations`.

To run it unattended, answer the prompts up front — each option takes a value, so pass `--use-uuid=1`, `--tenancy-name=Team` and `--run-migrations=1`, using `0` to opt out.

## Registering the styles

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels, follow the instructions in the [Filament documentation](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) first.

Add Curator's styles and views to your theme's CSS file — or your application's CSS file if you are using the standalone packages:

```css
@import '../../../../vendor/awcodes/filament-curator/resources/css/plugin.css';

@source '../../../../vendor/awcodes/filament-curator/resources/**/*.blade.php';
```

## Standalone forms

If you are using the standalone forms package rather than Panels, the picker's modal is not injected for you. Add it to your layout, normally just before the closing `body` tag:

```blade
<x-curator::modals.modal />
```

With Panels, the plugin handles this — see [Configuration](configuration.md).
