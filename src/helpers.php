<?php

use Awcodes\Curator\Curator;

if (! function_exists('curator')) {
    function curator(): Curator
    {
        return app(Curator::class);
    }
}
