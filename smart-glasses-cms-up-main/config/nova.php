<?php

use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Http\Middleware\Authorize;
use Laravel\Nova\Http\Middleware\BootTools;
use Laravel\Nova\Http\Middleware\DispatchServingNovaEvent;
use Laravel\Nova\Http\Middleware\HandleInertiaRequests;

return [
    'license_key' => env('NOVA_LICENSE_KEY'),

    'name' => env('NOVA_APP_NAME', env('APP_NAME')),
    'domain' => env('NOVA_DOMAIN_NAME', null),
    'path' => env('NOVA_PATH', '/settings'),

    'guard' => env('NOVA_GUARD', null),
    'passwords' => env('NOVA_PASSWORDS', null),

    'middleware' => [
        'web',
        HandleInertiaRequests::class,
        DispatchServingNovaEvent::class,
        BootTools::class,
    ],

    'api_middleware' => [
        'nova',
        Authenticate::class,
        Authorize::class,
    ],

    //"simple", "load-more", "links"
    'pagination' => 'simple',

    /*
    |--------------------------------------------------------------------------
    | Nova Storage Disk
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to define the default disk that
    | will be used to store files using the Image, File, and other file
    | related field types. You're welcome to use any configured disk.
    |
     */

    'storage_disk' => env('NOVA_STORAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Nova Currency
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to define the default currency
    | used by the Currency field within Nova. You may change this to a
    | valid ISO 4217 currency code to suit your application's needs.
    |
    */

    'currency' => 'KRW',

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | These configuration values allow you to customize the branding of the
    | Nova interface, including the primary color and the logo that will
    | be displayed within the Nova interface. This logo value must be
    | the absolute path to an SVG logo within the local filesystem.
    |
    */

    'brand' => [
        'logo' => resource_path('/amuz_logos/horizon_logo.svg'),

        'colors' => [
            "400" => "46, 110, 243, 0.5",
            "500" => "46, 110, 243",
            "600" => "46, 110, 243, 0.75",
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Nova Action Resource Class
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to specify a custom resource class
    | to use for action log entries instead of the default that ships with
    | Nova, thus allowing for the addition of additional UI form fields.
    |
    */

    'actions' => [
        'resource' => ActionResource::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Nova Impersonation Redirection URLs
    |--------------------------------------------------------------------------
    |
    | This configuration option allows you to specify a URL where Nova should
    | redirect an administrator after impersonating another user and a URL
    | to redirect the administrator after stopping impersonating a user.
    |
    */

    'impersonation' => [
        'started' => '/',
        'stopped' => '/',
    ],

];
