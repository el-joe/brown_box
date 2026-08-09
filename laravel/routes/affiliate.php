<?php

use App\Http\Controllers\Affiliate\Auth\LoginController;
use App\Http\Controllers\Affiliate\BalanceController;
use App\Http\Controllers\Affiliate\CommissionController;
use App\Http\Controllers\Affiliate\DashboardController;
use App\Http\Controllers\Affiliate\OrderController;
use App\Http\Controllers\Affiliate\PayoutController;
use App\Http\Controllers\Affiliate\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('affiliate')->name('affiliate.')->group(function (): void {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::middleware('affiliate.auth')->group(function (): void {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('orders')->name('orders.')->group(function (): void {
            Route::get('/', [OrderController::class, 'index'])->name('index');
        });

        Route::prefix('commissions')->name('commissions.')->group(function (): void {
            Route::get('/', [CommissionController::class, 'index'])->name('index');
        });

        Route::prefix('balance')->name('balance.')->group(function (): void {
            Route::get('/', [BalanceController::class, 'index'])->name('index');
        });

        Route::prefix('payouts')->name('payouts.')->group(function (): void {
            Route::get('/', [PayoutController::class, 'index'])->name('index');
            Route::post('/', [PayoutController::class, 'store'])->name('store');
        });

        Route::prefix('profile')->name('profile.')->group(function (): void {
            Route::get('/', [ProfileController::class, 'edit'])->name('edit');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
        });
    });
});
