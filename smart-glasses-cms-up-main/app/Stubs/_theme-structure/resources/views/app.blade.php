<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'AMUZ') }}</title>
        <link href="{{ asset('/assets/css/notosans_kr.css') }}" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite([
                "amuz-themes/DummyVendor/DummyName/resources/js/app.js",
                "amuz-themes/DummyVendor/DummyName/resources/js/Pages/{$page['component']}.vue"
                ])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
