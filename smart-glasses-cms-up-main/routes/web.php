<?php

use App\Http\Controllers\AnnouncementController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


//Route::get('/', function () {
//    return Inertia::render('Home');
//})->name('home');

Route::get('/', function () {
   return redirect('settings');
});

Route::name('announcement.')
    ->prefix('announcements')
    ->controller(AnnouncementController::class)
    ->group(function(){

    Route::get('/{slug?}', 'index')->name('index');

});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

});
