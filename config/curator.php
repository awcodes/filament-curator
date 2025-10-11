<?php

declare(strict_types=1);

return [
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
        'resource' => Awcodes\Curator\Resources\Media\MediaResource::class,
        'pages' => [
            'create' => Awcodes\Curator\Resources\Media\Pages\CreateMedia::class,
            'edit' => Awcodes\Curator\Resources\Media\Pages\EditMedia::class,
            'index' => Awcodes\Curator\Resources\Media\Pages\ListMedia::class,
        ],
        'schemas' => [
            'form' => Awcodes\Curator\Resources\Media\Schemas\MediaForm::class,
        ],
        'tables' => [
            'table' => Awcodes\Curator\Resources\Media\Tables\MediaTable::class,
        ],
    ],
    'url_provider' => Awcodes\Curator\Providers\GlideUrlProvider::class,
];
