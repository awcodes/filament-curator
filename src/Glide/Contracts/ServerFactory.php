<?php

namespace Awcodes\Curator\Glide\Contracts;

interface ServerFactory
{
    public function getFactory(): \League\Glide\ServerFactory|\League\Glide\Server;
}