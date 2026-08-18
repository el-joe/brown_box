<?php

use App\Http\Controllers\Website\AccountController;
use App\Http\Controllers\Website\BlogController;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Website\CheckoutController;
use App\Http\Controllers\Website\ContactController;
use App\Http\Controllers\Website\CouponController;
use App\Http\Controllers\Website\CustomerAuthController;
use App\Http\Controllers\Website\HomeController;
use App\Http\Controllers\Website\PaymentController;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\ProductListController;
use App\Http\Controllers\Website\ReviewController;
use App\Http\Controllers\Website\SearchController;
use App\Http\Controllers\Website\ShippingController;
use App\Http\Controllers\Website\StaticPageController;
use App\Http\Controllers\Website\TrackOrderController;
use App\Http\Controllers\Website\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('{lang}')->where(['lang' => 'ar|en'])->middleware(['web.locale', 'web.affiliate', 'web.seo'])->name('web.')->group(function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('products', [ProductListController::class, 'index'])->name('products.index');
    Route::get('categories/{categorySlug?}', [ProductListController::class, 'index'])->name('categories.show');
    Route::get('product/{slug}', [ProductController::class, 'show'])->name('products.show');

    Route::middleware('customer.auth')->group(function (): void {
        Route::post('products/{product}/reviews', [ReviewController::class, 'store'])->name('products.reviews.store');
    });

    Route::get('search', [SearchController::class, 'index'])->name('search.index');
    Route::get('search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

    Route::prefix('cart')->name('cart.')->group(function (): void {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('add', [CartController::class, 'add'])->name('add');
        Route::patch('{key}', [CartController::class, 'update'])->name('update');
        Route::delete('{key}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
    });

    Route::post('coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');

    Route::get('shipping/companies', [ShippingController::class, 'companies'])->name('shipping.companies');

    Route::prefix('wishlist')->name('wishlist.')->group(function (): void {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('toggle', [WishlistController::class, 'toggle'])->name('toggle');
    });

    Route::prefix('checkout')->name('checkout.')->middleware('customer.auth')->group(function (): void {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
        Route::get('success', [CheckoutController::class, 'success'])->name('success');
    });

    Route::post('payment/paymob/callback', [PaymentController::class, 'paymobCallback'])->name('payment.paymob.callback');
    Route::post('orders/{order}/payment-proof', [PaymentController::class, 'uploadProof'])->name('payment.upload-proof');

    Route::prefix('track-order')->name('track-order.')->group(function (): void {
        Route::get('/', [TrackOrderController::class, 'index'])->name('index');
        Route::post('/', [TrackOrderController::class, 'track'])->name('track');
    });

    Route::prefix('blog')->name('blog.')->group(function (): void {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('{slug}', [BlogController::class, 'show'])->name('show');
    });

    Route::get('page/{slug}', [StaticPageController::class, 'show'])->name('pages.show');

    Route::get('faqs', [StaticPageController::class, 'faqs'])->name('faqs.index');

    Route::prefix('contact')->name('contact.')->group(function (): void {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::post('/', [ContactController::class, 'submit'])->name('submit');
    });

    Route::prefix('account')->name('account.')->group(function (): void {
        Route::get('login', [CustomerAuthController::class, 'loginForm'])->name('login');
        Route::post('login', [CustomerAuthController::class, 'login'])->name('login.store');
        Route::get('register', [CustomerAuthController::class, 'registerForm'])->name('register');
        Route::post('register', [CustomerAuthController::class, 'register'])->name('register.store');

        Route::middleware('customer.auth')->group(function (): void {
            Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');

            Route::get('/', [AccountController::class, 'index'])->name('index');
            Route::get('orders', [AccountController::class, 'orders'])->name('orders');
            Route::get('orders/{order}', [AccountController::class, 'orderDetail'])->name('orders.show');
            Route::post('orders/{order}/refund', [AccountController::class, 'requestRefund'])->name('orders.refund');
            Route::get('wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
            Route::get('profile', [AccountController::class, 'profile'])->name('profile');
            Route::put('profile', [AccountController::class, 'updateProfile'])->name('profile.update');

            Route::prefix('addresses')->name('addresses.')->group(function (): void {
                Route::get('/', [AccountController::class, 'addresses'])->name('index');
                Route::post('/', [AccountController::class, 'storeAddress'])->name('store');
                Route::put('{address}', [AccountController::class, 'updateAddress'])->name('update');
                Route::delete('{address}', [AccountController::class, 'destroyAddress'])->name('destroy');
            });
        });
    });
});
