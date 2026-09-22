<?php

use App\Http\Controllers\SocialLoginController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::prefix('/amuz-cms-global')
    ->name('common.')
    ->group(function (Router $router) {

        // Social Login
        $router->get('/auth/{provider}/redirect', [SocialLoginController::class, "socialLiteRedirect"])
            ->name('social.redirect');
        $router->get('/auth/{provider}/callback', [SocialLoginController::class, "socialLiteCallback"])
            ->name('social.callback');

        // Language Switch
        $router->get('/global-common/language/{language}', function ($language) {
            Session()->put('locale', $language);
            return redirect()->back();
        })->name('language.update');
    });
