<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;

Route::view('/', 'spa')->name('spa');

Route::controller(SiteController::class)->group(function () {

    Route::get('home-data', 'home')->name('site.home.data');

    Route::get('session', 'session')->name('site.session');

    Route::post('login', 'login')
        ->middleware('throttle:5,1')
        ->name('site.login');

    Route::post('register', 'register')
        ->middleware('throttle:5,1')
        ->name('site.register');

});