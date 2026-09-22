<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\Translatable\Facades\Translatable;
use Symfony\Component\Finder\Finder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Translatable::fallback(
            fallbackLocale: 'ko',
            fallbackAny: true
        );
        $this->registerResources();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
    protected function registerResources(): void
    {
        $path = base_path(config('amuz.package_path'));
        $namespace = config('amuz.package_name_space');

        if(!is_dir($path)) return;

        foreach((new Finder())->in($path)->directories() as $dir){

            $name = Str::studly($dir->getFilename());
            $provider = "$namespace\\$name\\{$name}ServiceProvider";

            if(class_exists($provider)){
                $this->app->register($provider);
            }
        }
    }

    protected function mergeConfigFrom($path, $key): void
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            $config->set($key, array_merge(
                $config->get($key, []), require $path
            ));
        }
    }

    private function getAmuzPackages(): array
    {
        return Storage::disk('package')->directories();
    }
}
