<?php

namespace App\Providers;

use App\Models\User;
use App\Services\AbstractNovaConfigs;
use App\Settings\NovaMenu;
use CodencoDev\NovaGridSystem\NovaGridSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Jerry\ChatCard\ChatCard;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Outl1ne\NovaSettings\NovaSettings;
use Symfony\Component\Finder\Finder;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        Nova::style('noto_sans_kr', public_path('assets/css/notosans_kr.css'));
        Nova::style('cms-up-style', public_path('assets/css/cms-up-style.css'));

        $this->registerNovaConfigs();

        Nova::mainMenu(function (Request $request, Menu $menu) {
            $novaMenu = new NovaMenu();
            $configMenu = new NovaSettings();

            return $novaMenu->registerNovaMenu($request, $menu)
                ->append()
                ->items;
        });


        Nova::footer(function ($request) {
            return view('nova.layouts.footer')->render();
        });
    }

    private function registerNovaConfigs(): void
    {
        $directory = app_path('Settings/Configs');
        $namespace = "App\\Settings\\Configs\\";
        $configs = [];

        $OS_Separator = DIRECTORY_SEPARATOR === '/' ? '/' : '\\';

        $configResources = collect();
        foreach ((new Finder)->in($directory)->files() as $resource) {
            $resource = str_replace(
                '.php',
                '',
                $namespace.Str::afterLast($resource, $OS_Separator)
            );

            if (is_subclass_of($resource, AbstractNovaConfigs::class))  $configResources->add(new $resource);
        }

        foreach($configResources->sortBy('priority') as $config){
            NovaSettings::addSettingsFields(
                $config->fields(), $config->casts(), $config->pageName());
        }
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes(): void
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function (User $user) {
            return $user->hasAnyPermission('viewNova');
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards(): array
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools(): array
    {
        return [
            new \Badinansoft\LanguageSwitch\LanguageSwitch(),
            new NovaGridSystem(),
            (new \Sereny\NovaPermissions\NovaPermissions())->canSee(function ($request) {
                return $request->user()->isSuperAdmin();
            }),
            new NovaSettings()
        ];
    }

    public function cards()
    {
        return [
            new ChatCard(),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //다른 패키지들보다 리소스 등록 우선권을 갖게 해준다.
        Nova::serving(function (ServingNova $event) {
            $this->resources();
        });
    }
}
