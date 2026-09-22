<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Resource;
use Symfony\Component\Finder\Finder;
use ReflectionClass;
use ReflectionException;

abstract class AbstractPackageProvider extends ServiceProvider{
    protected string $namespace;
    /**
     * @throws ReflectionException
     */
    protected function resources($dir): array
    {
        $directory = $dir . '/Nova/Resources';
        $namespace = $this->namespace;
        $resources = [];

        $OS_Separator = DIRECTORY_SEPARATOR === '/' ? '/' : '\\';

        foreach ((new Finder)->in($directory)->files() as $resource) {
            $resource = str_replace(
                '.php',
                '',
                $namespace."\\Nova\\Resources\\".Str::afterLast($resource, $OS_Separator)
            );

            if (is_subclass_of($resource, Resource::class) &&
                ! (new ReflectionClass($resource))->isAbstract() &&
                ! (is_subclass_of($resource, ActionResource::class))) {
                $resources[] = $resource;
            }
        }

        return $resources;
    }
}
