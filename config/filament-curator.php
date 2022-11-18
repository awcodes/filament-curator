<?php

return [
    'load_styles' => true,
    'model' => \FilamentCurator\Models\Media::class,
    'media_resource' => \FilamentCurator\Resources\MediaResource::class,
    'label' => 'Media',
    'navigation_icon' => 'heroicon-o-photograph',
    'disk' => 'public',
    'directory' => 'media',
    'path_generator' => \FilamentCurator\Config\PathGenerator\DefaultPathGenerator::class,
    'visibility' => 'public',
    'preserve_file_names' => false,
    'accepted_file_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'application/pdf'],
    'max_width' => 5000,
    'min_size' => null,
    'max_size' => 5000,
    'rules' => [],
    'cloud_disks' => ['cloudinary', 's3'],
    'sizes' => [
        'medium' => ['width' => 640, 'height' => null, 'quality' => 60],
        'large' => ['width' => 1024, 'height' => null, 'quality' => 60],
    ],
];
