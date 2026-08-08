# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Code style
composer lint           # Run Laravel Pint (fix in place)
composer test:lint      # Dry-run style check

# Refactoring
composer refactor       # Run Rector
composer test:refactor  # Dry-run Rector check

# Static analysis
composer test:types     # Run PHPStan (level 5, larastan)

# Testing
composer test:unit      # Run Pest unit tests
composer test           # Run all checks (refactor, lint, types, unit)

# Run a single test file or method
vendor/bin/pest tests/src/Unit/Models/MediaTest.php
vendor/bin/pest --filter="test name or pattern"

# Frontend assets
npm run dev             # Dev build
npm run build           # Production build
```

## Architecture

This is a **Filament 4.x plugin** — a Laravel Composer package, not a standalone app. (This is the maintenance branch for Filament 4; changes usually start here and merge forward into `5.x`.) The entry points are:

- **`CuratorPlugin`** — registered with Filament panels via `CuratorPlugin::make()`. Configures all plugin defaults.
- **`CuratorServiceProvider`** — registers facades, routes, config, migrations, views, and Livewire components.
- **Three facades:** `Curator` (CuratorManager), `Glide` (GlideManager), `Curation` (CurationManager) — these hold runtime configuration and are the primary programmatic API.

### Key `src/` layout

| Path | Purpose |
|---|---|
| `Models/Media.php` | Eloquent model; has computed appends (`url`, `thumbnail_url`, `placeholder`, etc.) |
| `CuratorPlugin.php` | Plugin class; fluent config API forwarded to the managers |
| `Config/` | Manager classes (CuratorManager, GlideManager, CurationManager) |
| `Resources/` | Filament MediaResource (list, create, edit pages + form/table definitions) |
| `Components/Forms/` | `CuratorPicker` (form field), `Uploader`, `CuratorEditor` |
| `Components/Modals/` | `CuratorPanel` (Livewire component — the main media picker modal) |
| `Components/Tables/` | `CuratorColumn` |
| `Concerns/` | Traits: `CanUploadFiles`, `CanGeneratePaths`, etc. |
| `PathGenerators/` | Strategy pattern for storage paths (Default, Date, User) |
| `Glide/` | Glide image server integration (builder + response factory) |
| `Http/Controllers/` | `MediaController` — serves Glide-transformed images |
| `View/Components/` | Blade components: `Glider`, `Curation` |
| `helpers.php` | Global helper functions |

### Data flow for image serving

Uploaded files are stored via Laravel's filesystem (`disk` + `directory` columns on `Media`). Image transformations are served on-the-fly through `GlideManager` using the League Glide library, secured with a signed token. The `Media` model's computed URL appends (`thumbnail_url`, `medium_url`, `large_url`, `url`) call through the Glide facade.

### Testing

Tests use **Pest 4.x** and live in `tests/src/Unit/` and `tests/src/Feature/`. All tests extend `Awcodes\Curator\Tests\TestCase` which boots Orchestra Testbench with an in-memory SQLite database.

### CI

CI runs on the shared baseline in [`awcodes/.github`](https://github.com/awcodes/.github), called from a branch-scoped workflow (`ci-filament-4.yml` here, `ci-filament-5.yml` on `5.x`). Each file's `on:` is scoped to its own branch, so merging this one forward into `5.x` is harmless — it will not run there. CI reports four checks — `Tests`, `Lint`, `Static Analysis`, `Reformat` — mirroring `composer test`, so run that locally before pushing.

Two pinning rules apply: the `awcodes/.github` caller is pinned to the **moving major tag** `@v1` (never a SHA) so shared CI fixes propagate, and Dependabot is configured to ignore it. Third-party actions in this repo's own workflows are pinned to **full commit SHAs**.
