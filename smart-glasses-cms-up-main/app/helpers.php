<?php

use Symfony\Component\Finder\Finder;

if (! function_exists('theme_path')) {
    function theme_path($themeName = ''): string
    {
        return app()->joinPaths(base_path(),sprintf("amuz-themes".DIRECTORY_SEPARATOR."%s",$themeName));
    }
}

if (! function_exists('package_path')) {
    function package_path($packageName = ''): string
    {
        return app()->joinPaths(base_path(),sprintf("amuz-packages".DIRECTORY_SEPARATOR."%s",$packageName));
    }
}

if (! function_exists('package_config_path')) {
    function package_config_path(string $packageName,string $path = ''): string
    {
        return sprintf("%s".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."%s",package_path($packageName),$path);
    }
}

if (! function_exists('package_database_path')) {
    function package_database_path(string $packageName,string $path = ''): string
    {
        return sprintf("%s".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."database".DIRECTORY_SEPARATOR."%s",package_path($packageName),$path);
    }
}

if (! function_exists('package_resource_path')) {
    function package_resource_path(string $packageName,string $path = ''): string
    {
        return sprintf("%s".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."resources".DIRECTORY_SEPARATOR."%s",package_path($packageName),$path);
    }
}

if (! function_exists('package_lang_path')) {
    function package_lang_path(string $packageName,string $path = ''): string
    {
        return sprintf("%s".DIRECTORY_SEPARATOR."%s",package_resource_path($packageName,'lang'),$path);
    }
}

if (! function_exists('amuz_packages')) {
    function amuz_packages(): array
    {
        $packages = [];
        foreach ((new Finder)->in(package_path())->depth(0)->directories() as $amuzPackage) {
            $packages[$amuzPackage->getFilename()] = $amuzPackage->getRealPath();
        }
        return $packages;
    }
}

if (! function_exists('amuz_themes')) {
    function amuz_themes(): array
    {
        $themes = [];
        $basePath = theme_path();

        foreach ((new Finder)->in($basePath)->depth('== 1')->directories() as $amuzTheme) {
            $relativePath = str_replace($basePath, '', $amuzTheme->getRealPath());
            $themes[$relativePath] = $amuzTheme->getRealPath();
        }

        return $themes;
    }
}
