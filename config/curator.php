<?php

use Awcodes\Curator\Curations\ThumbnailPreset;
use Awcodes\Curator\Glide\DefaultServerFactory;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\MediaResource;

return [
    'accepted_file_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
    ],
    'cloud_disks' => [
        's3',
        'cloudinary',
        'imgix',
    ],
    'cropper' => [
        'check_cross_origin' => true,
    ],
    'curation_formats' => [
        'jpg',
        'jpeg',
        'webp',
        'png',
        'avif',
    ],
    'curation_presets' => [
        ThumbnailPreset::class,
    ],
    'directory' => 'media',
    'disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
    'glide' => [
        'server' => DefaultServerFactory::class,
        'fallbacks' => [],
        'route_path' => 'curator',
    ],
    'image_crop_aspect_ratio' => null,
    'image_resize_mode' => null,
    'image_resize_target_height' => null,
    'image_resize_target_width' => null,
    'is_limited_to_directory' => false,
    'is_tenant_aware' => true,
    'tenant_ownership_relationship_name' => 'tenant',
    'max_size' => 5000,
    'model' => Media::class,
    'min_size' => 0,
    'path_generator' => null,
    'resources' => [
        'label' => 'Media',
        'plural_label' => 'Media',
        'navigation_group' => null,
        'cluster' => null,
        'navigation_label' => 'Media',
        'navigation_icon' => 'heroicon-o-photo',
        'navigation_sort' => null,
        'navigation_count_badge' => false,
        'resource' => MediaResource::class,
    ],
    'should_preserve_filenames' => false,
    'should_register_navigation' => true,
    'should_check_exists' => true,
    'visibility' => 'public',
    'tabs' => [
        'display_curation' => true,
        'display_upload_new' => true,
    ],
    'multi_select_key' => 'metaKey',
    'table' => [
        'layout' => 'grid',
    ],
];
