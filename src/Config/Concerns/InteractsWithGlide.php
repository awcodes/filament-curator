<?php

namespace Awcodes\Curator\Config\Concerns;

use Awcodes\Curator\Glide\GlideServer;

trait InteractsWithGlide
{
    public function getGlideServer(): GlideServer
    {
        return app(GlideServer::class);
    }
}
