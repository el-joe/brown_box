<?php

namespace App\Providers;

use App\Repositories\AccountingEntryRepository;
use App\Repositories\AffiliateCommissionRepository;
use App\Repositories\AffiliateRepository;
use App\Repositories\BannerRepository;
use App\Repositories\BlogCategoryRepository;
use App\Repositories\BlogPostRepository;
use App\Repositories\BrandRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\Contracts\AccountingEntryRepositoryInterface;
use App\Repositories\Contracts\AffiliateCommissionRepositoryInterface;
use App\Repositories\Contracts\AffiliateRepositoryInterface;
use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Repositories\Contracts\BlogCategoryRepositoryInterface;
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\FlashSaleRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PayoutRequestRepositoryInterface;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Repositories\Contracts\RefundRequestRepositoryInterface;
use App\Repositories\Contracts\SearchSuggestionRepositoryInterface;
use App\Repositories\Contracts\SeoPageRepositoryInterface;
use App\Repositories\Contracts\ShippingCompanyRepositoryInterface;
use App\Repositories\Contracts\ShippingRateRepositoryInterface;
use App\Repositories\Contracts\StaticPageRepositoryInterface;
use App\Repositories\Contracts\StockRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use App\Repositories\CouponRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\FlashSaleRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PayoutRequestRepository;
use App\Repositories\ProductAttributeRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseRepository;
use App\Repositories\RefundRequestRepository;
use App\Repositories\SearchSuggestionRepository;
use App\Repositories\SeoPageRepository;
use App\Repositories\ShippingCompanyRepository;
use App\Repositories\ShippingRateRepository;
use App\Repositories\StaticPageRepository;
use App\Repositories\StockRepository;
use App\Repositories\TagRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    private array $repositoryBindings = [
        CategoryRepositoryInterface::class => CategoryRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        ProductAttributeRepositoryInterface::class => ProductAttributeRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        StockRepositoryInterface::class => StockRepository::class,
        AffiliateRepositoryInterface::class => AffiliateRepository::class,
        AffiliateCommissionRepositoryInterface::class => AffiliateCommissionRepository::class,
        CouponRepositoryInterface::class => CouponRepository::class,
        CustomerRepositoryInterface::class => CustomerRepository::class,
        FlashSaleRepositoryInterface::class => FlashSaleRepository::class,
        WarehouseRepositoryInterface::class => WarehouseRepository::class,
        TransactionRepositoryInterface::class => TransactionRepository::class,
        AccountingEntryRepositoryInterface::class => AccountingEntryRepository::class,
        ShippingCompanyRepositoryInterface::class => ShippingCompanyRepository::class,
        ShippingRateRepositoryInterface::class => ShippingRateRepository::class,
        RefundRequestRepositoryInterface::class => RefundRequestRepository::class,
        PayoutRequestRepositoryInterface::class => PayoutRequestRepository::class,
        BrandRepositoryInterface::class => BrandRepository::class,
        BannerRepositoryInterface::class => BannerRepository::class,
        TagRepositoryInterface::class => TagRepository::class,
        SearchSuggestionRepositoryInterface::class => SearchSuggestionRepository::class,
        NotificationRepositoryInterface::class => NotificationRepository::class,
        PurchaseRepositoryInterface::class => PurchaseRepository::class,
        StaticPageRepositoryInterface::class => StaticPageRepository::class,
        SeoPageRepositoryInterface::class => SeoPageRepository::class,
        BlogCategoryRepositoryInterface::class => BlogCategoryRepository::class,
        BlogPostRepositoryInterface::class => BlogPostRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->repositoryBindings as $interface => $concrete) {
            $this->app->bind($interface, $concrete);
        }
    }
}
