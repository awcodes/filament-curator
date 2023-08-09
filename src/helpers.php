<?php

use RocketFirm\Curator\Curator;

if (! function_exists('curator')) {
    function curator(): Curator
    {
        return app('curator');
    }
}
