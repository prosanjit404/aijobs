<?php

use Illuminate\Support\Facades\Route;


Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    
    Route::controller('LoginController')->group(function () {
        Route::get('login', 'showLoginForm')->name('login');
        Route::get('forget', 'forget')->name('forget');

        Route::post('login', 'login')->name('login.submit');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->name('register.submit');
    });
});

Route::middleware('auth')->name('user.')->group(function () {
    Route::namespace('User')->group(function () {

        Route::controller('ActivityController')->group(function () {
            Route::get('activity', 'index')->name('activity');
        });


    });
});
