<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Js;

test('the panel modal passes its key to the insert listener as a javascript string', function () {
    Storage::fake('public');

    $key = "data.it's";

    $html = view('curator::components.modals.curator-panel', [
        'key' => $key,
        'settings' => [],
    ])->render();

    $handler = 'callSchemaComponentMethod(' . Js::from($key)->toHtml() . ", 'updateState', \$event.detail); close()";

    expect($html)->toContain('x-on:insert-media="' . htmlspecialchars('$wire.$parent.' . $handler, ENT_QUOTES) . '"');
});
