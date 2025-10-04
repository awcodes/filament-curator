# Breaking Changes: Curator 3.x to 4.x

This document outlines the breaking changes when upgrading from Curator 3.x to 4.x.

## 🚨 Major Breaking Changes

### 1. PHP Version Requirement
- **3.x**: Required PHP ^8.1
- **4.x**: Requires PHP ^8.2
- **Impact**: Applications running PHP 8.1 will need to upgrade to PHP 8.2+

### 2. Filament Version Requirement
- **3.x**: Required Filament ^3.2.139 (dev dependency)
- **4.x**: Requires Filament ^4.0 (main dependency)
- **Impact**: Complete Filament upgrade required, breaking all Filament-related code

### 3. Dependency Changes
- **Removed**: `intervention/image` (^2.7.2)
- **Removed**: `league/glide-symfony` (^2.0.1)
- **Added**: `league/glide` (^3.0)
- **Impact**: Image processing and Glide integration completely changed

## 🏗️ Architecture Changes

### 4. Configuration System Overhaul
The entire configuration system has been restructured:

**3.x Configuration Structure:**
```php
// config/curator.php
'accepted_file_types' => [...],
'cloud_disks' => [...],
'cropper' => [...],
'curation_formats' => [...],
'curation_presets' => [...],
'directory' => 'media',
'disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
'glide' => [...],
'image_crop_aspect_ratio' => null,
'image_resize_mode' => null,
'image_resize_target_height' => null,
'image_resize_target_width' => null,
'is_limited_to_directory' => false,
'is_tenant_aware' => true,
'tenant_ownership_relationship_name' => 'tenant',
'max_size' => 5000,
'model' => \Awcodes\Curator\Models\Media::class,
'min_size' => 0,
'path_generator' => null,
'resources' => [...],
'should_preserve_filenames' => false,
'should_register_navigation' => true,
'should_check_exists' => true,
'visibility' => 'public',
'tabs' => [...],
'multi_select_key' => 'metaKey',
'table' => [...],
```

**4.x Configuration Structure:**
```php
// config/curator.php
'curation_formats' => Awcodes\Curator\Enums\PreviewableExtensions::toArray(),
'default_disk' => env('CURATOR_DEFAULT_DISK', 'public'),
'default_visibility' => 'public',
'features' => [
    'curations' => true,
    'file_swap' => true,
    'directory_restriction' => false,
    'tenancy' => [
        'enabled' => false,
        'relationship_name' => null,
    ],
],
'glide_token' => env('CURATOR_GLIDE_TOKEN'),
'model' => Awcodes\Curator\Models\Media::class,
'path_generator' => null,
'resource' => [...],
'url_provider' => Awcodes\Curator\Providers\GlideUrlProvider::class,
```

**Migration Required**: Complete configuration file rewrite needed.

### 5. Manager Classes Introduced
New manager classes replace direct configuration access:

- `CuratorManager` - Main configuration management
- `CurationManager` - Curation preset management  
- `GlideManager` - Glide server configuration

**Breaking Change**: All configuration access patterns changed from `config('curator.key')` to manager methods.

### 6. Facade System Overhaul
**3.x**: No facades
**4.x**: Three new facades introduced:
- `Curator` facade for main functionality
- `Curation` facade for curation management
- `Glide` facade for Glide operations

**Impact**: Code using direct configuration access needs to use facades instead.

## 🗄️ Database Changes

### 7. Migration System Changes
**3.x**: Multiple migration stubs:
- `create_media_table.php.stub`
- `add_tenant_aware_column_to_media_table.php.stub` 
- `upgrade_media_table.php.stub`

**4.x**: Single migration stub:
- `migration.stub` with hardcoded table name `curator`

**Breaking Changes**:
- Table name changed from configurable to hardcoded `curator`
- Migration structure completely different
- Tenant awareness handled differently

### 8. Model Changes
**Media Model Breaking Changes**:
- Added `#[ObservedBy([MediaObserver::class])]` attribute
- Changed `$guarded = []` to explicit `$fillable` array
- Added `$table = 'curator'` property
- Removed `resizable` and `size_for_humans` computed attributes
- Added `placeholder` computed attribute
- Changed URL generation logic completely
- Removed EXIF encoding/decoding methods
- Changed method signatures (added return types)

## 🎨 Component Changes

### 9. Resource Structure Overhaul
**3.x**: Single `MediaResource.php` file
**4.x**: Split into multiple files:
- `src/Resources/Media/MediaResource.php`
- `src/Resources/Media/Pages/CreateMedia.php`
- `src/Resources/Media/Pages/EditMedia.php` 
- `src/Resources/Media/Pages/ListMedia.php`
- `src/Resources/Media/Schemas/MediaForm.php`
- `src/Resources/Media/Tables/MediaTable.php`

**Impact**: Custom resource extensions need complete rewrite.

### 10. Form Component Changes
**CuratorPicker Breaking Changes**:
- Changed from `Forms\Components\Component` to `Filament\Schemas\Components\Component`
- Updated action method signatures
- Changed icon references (e.g., `'heroicon-s-arrows-up-down'` to `Heroicon::ArrowsPointingOut`)
- Modified modal handling completely
- Updated relationship handling logic
- Changed configuration access patterns

### 11. Service Provider Changes
**Breaking Changes**:
- Removed migration publishing
- Changed command registration
- Updated asset registration
- Modified binding patterns
- Changed component registration

## 🔧 Helper Functions

### 12. Helper Function Changes
**Removed Functions**:
- `is_panel_auth_route()`

**Modified Functions**:
- `is_media_resizable()` - Changed parameter from `$type` to `$ext`
- `get_media_items()` - Updated return type annotations

**New Functions**:
- `curator()` - Returns CuratorManager instance
- `glide()` - Returns GlideManager instance  
- `curation()` - Returns CurationManager instance
- `glide_builder()` - Returns GlideBuilder instance

## 🌐 Localization Changes

### 13. Language File Removals
**Removed Language Packs**:
- Bengali (bn)
- Greek (el) 
- Hungarian (hu)
- Korean (ko)
- Lithuanian (lt)
- Dutch (nl)
- Polish (pl)
- Portuguese Brazil (pt_BR)
- Slovak (sk)
- Turkish (tr)
- Uzbek (uz)
- Vietnamese (vi)
- Chinese Simplified (zh_CN)

**Impact**: Applications using these languages will lose translations.

## 🎯 API Changes

### 14. Method Signature Changes
**Media Model**:
- All attribute methods now have explicit return types
- `getSignedUrl()` method removed
- `getSizeForHumans()` method removed
- EXIF handling methods removed

**CuratorPicker**:
- Action methods updated with new Filament 4 patterns
- Modal handling completely changed
- Icon references updated to enum format

### 15. Configuration Access Patterns
**3.x Pattern**:
```php
config('curator.disk')
config('curator.is_tenant_aware')
config('curator.tenant_ownership_relationship_name')
```

**4.x Pattern**:
```php
Curator::getDiskName()
Curator::isTenantAware()
Curator::getTenantOwnershipRelationshipName()
```

## 📦 Package Structure

### 16. File Structure Changes
**Removed Files**:
- `src/Support/Helpers.php`
- `src/Curations/ThumbnailPreset.php`
- `src/Glide/Contracts/ServerFactory.php`
- `src/Glide/DefaultServerFactory.php`
- `tailwind.config.js`
- `postcss.config.cjs`

**Added Files**:
- Multiple new enum classes
- New manager classes
- New facade classes
- New provider classes
- New DTO classes

## 🚀 Migration Steps

### Required Actions for Upgrade:

1. **Upgrade PHP to 8.2+**
2. **Upgrade Filament to 4.x**
3. **Update composer.json dependencies**
4. **Rewrite configuration file** - Complete restructure needed
5. **Update database migrations** - New table structure
6. **Update all configuration access** - Use new manager classes/facades
7. **Rewrite custom resource extensions** - New file structure
8. **Update form component usage** - New Filament 4 patterns
9. **Update helper function calls** - Changed signatures
10. **Handle removed language packs** - Find alternatives or add custom translations
11. **Update custom Media model extensions** - New attributes and methods
12. **Rewrite Glide integration** - New Glide 3.x API

### Configuration Migration Example:

**Before (3.x)**:
```php
'disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
'is_tenant_aware' => true,
'tenant_ownership_relationship_name' => 'tenant',
```

**After (4.x)**:
```php
'default_disk' => env('CURATOR_DEFAULT_DISK', 'public'),
'features' => [
    'tenancy' => [
        'enabled' => true,
        'relationship_name' => 'tenant',
    ],
],
```

## ⚠️ Critical Notes

- This is a **major version upgrade** with extensive breaking changes
- **Complete codebase review required** before upgrading
- **Database migration required** - table structure changed
- **Configuration file must be completely rewritten**
- **All Filament-related code needs Filament 4.x compatibility**
- **Custom extensions and integrations will need significant updates**

## 📚 Additional Resources

- Review Filament 4.x upgrade guide
- Check new Curator 4.x documentation
- Test thoroughly in development environment
- Consider gradual migration strategy for large applications
