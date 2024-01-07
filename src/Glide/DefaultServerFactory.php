<?php

namespace Awcodes\Curator\Glide;

use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;

class DefaultServerFactory implements Contracts\ServerFactory
{
    public function getFactory(): ServerFactory | Server
    {
        return ServerFactory::create([
            'driver' => 'gd',
            'response' => new SymfonyResponseFactory(app('request')),
            'source' => storage_path('app'),
            'source_path_prefix' => 'public',
            'cache' => storage_path('app'),
            'cache_path_prefix' => '.cache',
            'max_image_size' => 2000 * 2000,
        ]);
    }
}
