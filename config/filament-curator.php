<?php

return [
    'disk' => 'public',
    'directory' => 'trov',
    'preserve_file_names' => true,
    'sizes' => [
        'thumbnail' => ['width' => 200, 'height' => 200, 'quality' => 60],
        'medium' => ['width' => 640, 'height' => null, 'quality' => 60],
        'large' => ['width' => 1024, 'height' => null, 'quality' => 60],
    ],
];
