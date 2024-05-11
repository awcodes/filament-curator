<?php

return [
    'default_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
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
    'model' => \Awcodes\Curator\Models\Media::class,
    'path_generator' => null,
    'resource' => [
        'label' => 'Media',
        'plural_label' => 'Media',
        'default_layout' => 'grid',
        'navigation' => [
            'group' => null,
            'icon' => 'heroicon-o-photo',
            'sort' => null,
            'should_register' => true,
            'should_show_badge' => false,
        ],
        'resource' => \Awcodes\Curator\Resources\MediaResource::class,
        'pages' => [
            'create' => \Awcodes\Curator\Resources\MediaResource\CreateMedia::class,
            'edit' => \Awcodes\Curator\Resources\MediaResource\EditMedia::class,
            'index' => \Awcodes\Curator\Resources\MediaResource\ListMedia::class,
        ],
    ],
];
