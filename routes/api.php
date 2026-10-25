<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WingoController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\StarpagoPaymentController;
use App\Http\Controllers\Admin\WithdrawReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// SterGo Deposit Notify
Route::post('starpago/notify', [StarpagoPaymentController::class, 'notify'])->name('starpago.payment.notify');
//SterGO Deposit notify
Route::post('starpago/payout/notify', [WithdrawReportController::class, 'payoutHandleNotify'])->name('starpago.payout.notify');






//OkeY deposit Success callback function
Route::post('payment/deposit/callback', [StarpagoPaymentController::class, 'callback'])->name('payment.deposit.callback');
// OkeY PayOut callback function
Route::post('okpay/payout/callback', [StarpagoPaymentController::class, 'payoutCallback'])->name('okpay.payout.callback');


Route::get('current-round', [HomeController::class, 'currentRound']);
