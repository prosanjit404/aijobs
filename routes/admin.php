<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Auth')->group(function () {
    Route::controller('GiftCodeAdminController')->group(function () {
        Route::get('/admin/gificode', 'index')->name('admin.gift.index');
    
        Route::post('/admin/gificode/verify', 'verifyPassword')->name('admin.gift.verify');
        Route::post('/admin/gificode/logout', 'logoutPassword')->name('admin.gift.logout');
    
        Route::post('/admin/gificode/generate', 'generate')->name('admin.gift.generate');
        Route::put('/admin/gificode/update/{giftCode}', 'update')->name('admin.gift.update');
    });
    
});



Route::namespace('Auth')->group(function () {
    Route::middleware('admin.guest')->group(function(){
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login')->name('login.submit');
            Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
        });
    });
});
Route::middleware('admin')->group(function () {
    Route::controller('AdminController')->group(function(){
        Route::get('dashboard', 'dashboard')->name('dashboard');
    });
    Route::controller('ManageUsersController')->prefix('users')->name('users.')->group(function(){
        Route::get('/', 'index')->name('list'); 
        Route::get('ajax-list', 'ajaxList')->name('ajax.list');
        Route::get('details/{id}', 'details')->name('details.data');
        Route::post('/store', 'store')->name('store');

        Route::get('refferal/details',  'refferalusersAllData')->name('refferalusersAllData');

        Route::post('balance-update/{id}', 'balanceUpdate')->name('balance.update');
        Route::post('change-password/{id}', 'changePassword')->name('change.password');
        Route::post('status-update/{id}', 'statusUpdate')->name('status.update');
        Route::post('login-as-user/{id}', 'loginAsUser')->name('login.as.user');
        Route::get('turnover-required',  'turnoverForm')->name('turnover.form');
        Route::post('turnover-required',  'turnoverUpdate')->name('turnover.update');
    });
    Route::controller('GiftCodeController')->prefix('gift')->name('gift.')->group(function(){
        Route::get('/', 'index')->name('index'); 
        Route::post('generate', 'generate')->name('generate');
        Route::put('update/{giftCode}', 'update')->name('update');
    });

    Route::controller('GameControllController')->prefix('game-control')->name('game_control.')->group(function(){
        Route::get('/','index')->name('index');
        Route::post('game-control', 'store')->name('store');
    });
    

    Route::controller('WingoGameController')->name('wingo.')->prefix('wingo')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/details/{id}', 'show')->name('show');


        // Route::get('wingo-game/details/{type}', 'details')->name('color');

        // Route::post('color-game/set-manual', 'setManual')->name('color.setManual');
        // Route::post('color-game/unset-manual', 'unsetManual')->name('color.unsetManual');
        // Route::get('color-game/live-data/{type}', 'liveData')->name('color.liveData');




        Route::get('wingo-game/details/{type}', 'details')->name('color.details');
        Route::get('/live-data/{type}', 'liveData')->name('color.liveData');

        // current running round
        Route::post('/set-manual', 'setManual')->name('color.setManual');
        Route::post('/unset-manual', 'unsetManual')->name('color.unsetManual');

        // future rounds
        Route::post('/set-future-results', 'setFutureResults')->name('color.setFutureResults');
        Route::post('/set-round-result', 'setRoundResult')->name('color.setRoundResult');
        Route::post('/clear-future-round', 'clearFutureRound')->name('color.clearFutureRound');


    });

    
    Route::controller('GeneralSettingController')->prefix('general-setting')->name('general.setting.')->group(function(){
        Route::get('/','index')->name('index');
        Route::post('update', 'update')->name('update');

            Route::get('password', 'passwordForm')->name('password.form');
            Route::post('password/update', 'passwordUpdate')->name('password.update');
    });
    
    Route::controller('ReferralController')->name('referrals.')->prefix('referrals')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'update')->name('update');
        Route::get('status/{id}', 'status')->name('status');
        
        
    
    Route::get('deposit-method-commissions', 'depindex')->name('deposit-method-commissions.index');
    Route::post('deposit-method-commissions/update',  'depupdate')->name('deposit-method-commissions.update');
        
    
    });
    
    
    Route::controller('HomeBannerController')->name('home.banners.')->prefix('home-banners')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('delete/{id}', 'destroy')->name('destroy');
    });

    Route::controller('AnnouncementController')->name('announcements.')->prefix('announcements')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });
    Route::controller('AviatorSettingsController')->name('aviator.settings.')->prefix('aviator/settings')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('show',  'show')->name('show');
            Route::post('update',  'update')->name('update');
    });
    Route::controller('AviatorRoundReportController')->name('aviator.reports.')->prefix('aviator/reports')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{round}', 'show')->name('show');
    });
    Route::controller('DepositReportController')->name('deposits.index.')->prefix('deposits')->group(function () {
        Route::get('/', 'index')->name('index');
    });
    Route::controller('WithdrawReportController')->name('withdraws.report.')->prefix('withdraws/report')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/processing/{id}', 'processing')->name('processing');
        Route::post('/approve/{id}', 'approve')->name('approve');
        Route::post('/reject/{id}', 'reject')->name('reject');
        
        
        Route::get('/withdraw-wallets','walletindex')->name('withdraw-wallets.index');
        Route::post('/withdraw-wallets','store')->name('withdraw-wallets.store');
        Route::put('/withdraw-wallets/{wallet}','update')->name('withdraw-wallets.update');
        Route::post('/withdraw-wallets/{wallet}/activate','activate')->name('withdraw-wallets.activate');
        Route::delete('/withdraw-wallets/{wallet}','destroy')->name('withdraw-wallets.destroy');
    });
    Route::controller('DepositCommissionController')->name('deposit-commissions.')->prefix('deposit-commissions')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::post('/delete/{id}', 'destroy')->name('delete');
        Route::post('/toggle/{id}', 'toggle')->name('toggle');
    });

    Route::controller('VipAnalyticsController')->name('vip.analytics.')->prefix('vip/analytics')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/vip-analytics/{user}', 'show')->name('show');
    });
    
});

