<?php

use App\Http\Controllers\SiteController;
use App\Http\Controllers\SpaPageController;
use Illuminate\Support\Facades\Route;

// One HTML shell. Everything after # is handled by Vue Router.
Route::view('/', 'spa')->name('spa');

// Existing SiteController methods - keep the existing auth/business logic.
Route::controller(SiteController::class)->group(function () {
    Route::get('/home-data', 'home')->name('site.home.data');
    Route::get('/session', 'session')->name('site.session');

    Route::post('/login', 'login')->middleware('throttle:5,1')->name('site.login');
    Route::post('/register', 'register')->middleware('throttle:5,1')->name('site.register');

    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard-data', 'dashboard')->name('site.dashboard.data');
        Route::post('/logout', 'logout')->name('site.logout');
    });
});

// Laravel owns the page props; Vue owns the URL fragment and components.
Route::prefix('spa/pages')->name('spa.pages.')->controller(SpaPageController::class)->group(function () {
    Route::get('/home', 'home')->name('home');
    Route::get('/login', 'login')->name('login');
    Route::get('/register', 'register')->name('register');
    Route::get('/dashboard', 'dashboard')->middleware('auth:web')->name('dashboard');
});
