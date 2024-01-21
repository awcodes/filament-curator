<?php

return [
    'glide_token' => env('CURATOR_GLIDE_TOKEN'),
    'is_limited_to_directory' => false,
    'is_tenant_aware' => true,
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
    ]
];
