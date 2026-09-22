<?php

declare(strict_types=1);

return [
    'directory' => env('THEMES_DIR', 'amuz-themes'),
    'symlink_path' => 'themes',
    'symlink_relative' => false,
    'fallback_theme' => "amuz/default",

    'cache' => [
        'enabled'  => false,
        'key'      => 'themes-manager',
        'lifetime' => 86400,
    ],

    'composer' => [
        'vendor' => null,
        'author' => [
            'name'  => null,
            'email' => null,
        ],
    ],
];
