<?php

namespace App\Providers;

use App\Models\AffiliateCommission;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Order;
use App\Models\Product;
use App\Observers\AffiliateCommissionObserver;
use App\Observers\CatalogCacheObserver;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        AffiliateCommission::observe(AffiliateCommissionObserver::class);
        Category::observe(CatalogCacheObserver::class);
        FlashSale::observe(CatalogCacheObserver::class);
        Product::observe(CatalogCacheObserver::class);

        View::composer('website.layouts.app', function ($view): void {
            $view->with('headerCategories', Category::query()->active()->roots()->orderBy('sort_order')->get());
        });

        View::composer('affiliate.layouts.app', function ($view): void {
            $customer = Auth::guard('affiliate')->user();
            $affiliate = $customer?->affiliate;

            $view->with('affiliateStats', $affiliate ? [
                'available_balance' => (float) $affiliate->balance,
                'total_earned' => (float) $affiliate->total_earned,
                'pending_commissions' => (float) $affiliate->commissions()->where('status', 'pending')->sum('amount'),
                'total_orders' => $affiliate->orders()->count(),
            ] : null);
        });
    }
}
