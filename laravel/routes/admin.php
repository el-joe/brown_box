<?php

use App\Http\Controllers\Admin\AccountingController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\GatewayController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OpeningBalanceController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductAttributeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\RefundRequestController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SearchSuggestionController;
use App\Http\Controllers\Admin\SeoPageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingCompanyController;
use App\Http\Controllers\Admin\StaticPageController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::middleware('admin.auth')->group(function (): void {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('categories')->name('categories.')->group(function (): void {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('create', [CategoryController::class, 'create'])->name('create');
            Route::post('validate', [CategoryController::class, 'validateCategory'])->name('validate');
            Route::post('reorder', [CategoryController::class, 'reorder'])->name('reorder');
            Route::patch('{category}/toggle-active', [CategoryController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::get('{category}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('{category}/validate', [CategoryController::class, 'validateCategory'])->name('update.validate');
            Route::put('{category}', [CategoryController::class, 'update'])->name('update');
            Route::delete('{category}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('brands')->name('brands.')->group(function (): void {
            Route::get('/', [BrandController::class, 'index'])->name('index');
            Route::get('data', [BrandController::class, 'data'])->name('data');
            Route::get('create', [BrandController::class, 'create'])->name('create');
            Route::post('validate', [BrandController::class, 'validateBrand'])->name('validate');
            Route::patch('{brand}/toggle-active', [BrandController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [BrandController::class, 'store'])->name('store');
            Route::get('{brand}/edit', [BrandController::class, 'edit'])->name('edit');
            Route::put('{brand}/validate', [BrandController::class, 'validateBrand'])->name('update.validate');
            Route::put('{brand}', [BrandController::class, 'update'])->name('update');
            Route::delete('{brand}', [BrandController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('blog-categories')->name('blog-categories.')->group(function (): void {
            Route::get('/', [BlogCategoryController::class, 'index'])->name('index');
            Route::get('create', [BlogCategoryController::class, 'create'])->name('create');
            Route::post('validate', [BlogCategoryController::class, 'validateBlogCategory'])->name('validate');
            Route::patch('{blog_category}/toggle-active', [BlogCategoryController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [BlogCategoryController::class, 'store'])->name('store');
            Route::get('{blog_category}/edit', [BlogCategoryController::class, 'edit'])->name('edit');
            Route::put('{blog_category}/validate', [BlogCategoryController::class, 'validateBlogCategory'])->name('update.validate');
            Route::put('{blog_category}', [BlogCategoryController::class, 'update'])->name('update');
            Route::delete('{blog_category}', [BlogCategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('blog')->name('blog.')->group(function (): void {
            Route::get('/', [BlogController::class, 'index'])->name('index');
            Route::get('data', [BlogController::class, 'data'])->name('data');
            Route::get('create', [BlogController::class, 'create'])->name('create');
            Route::post('validate', [BlogController::class, 'validateBlogPost'])->name('validate');
            Route::patch('{blog}/toggle-active', [BlogController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [BlogController::class, 'store'])->name('store');
            Route::get('{blog}/edit', [BlogController::class, 'edit'])->name('edit');
            Route::put('{blog}/validate', [BlogController::class, 'validateBlogPost'])->name('update.validate');
            Route::put('{blog}', [BlogController::class, 'update'])->name('update');
            Route::delete('{blog}', [BlogController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('products')->name('products.')->group(function (): void {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('data', [ProductController::class, 'data'])->name('data');
            Route::get('generate-sku', [ProductController::class, 'generateSku'])->name('generate-sku');
            Route::get('create', [ProductController::class, 'create'])->name('create');
            Route::post('validate', [ProductController::class, 'validateProduct'])->name('validate');
            Route::patch('{product}/toggle-active', [ProductController::class, 'toggleActive'])->name('toggle-active');
            Route::patch('{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('{product}/validate', [ProductController::class, 'validateProduct'])->name('update.validate');
            Route::put('{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('attributes')->name('attributes.')->group(function (): void {
            Route::get('/', [ProductAttributeController::class, 'index'])->name('index');
            Route::get('data', [ProductAttributeController::class, 'data'])->name('data');
            Route::get('create', [ProductAttributeController::class, 'create'])->name('create');
            Route::post('validate', [ProductAttributeController::class, 'validateAttribute'])->name('validate');
            Route::post('/', [ProductAttributeController::class, 'store'])->name('store');
            Route::get('{attribute}/edit', [ProductAttributeController::class, 'edit'])->name('edit');
            Route::put('{attribute}/validate', [ProductAttributeController::class, 'validateAttribute'])->name('update.validate');
            Route::put('{attribute}', [ProductAttributeController::class, 'update'])->name('update');
            Route::delete('{attribute}', [ProductAttributeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('coupons')->name('coupons.')->group(function (): void {
            Route::get('/', [CouponController::class, 'index'])->name('index');
            Route::get('data', [CouponController::class, 'data'])->name('data');
            Route::get('generate-code', [CouponController::class, 'generateCode'])->name('generate-code');
            Route::get('create', [CouponController::class, 'create'])->name('create');
            Route::post('validate', [CouponController::class, 'validateCoupon'])->name('validate');
            Route::patch('{coupon}/toggle-active', [CouponController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [CouponController::class, 'store'])->name('store');
            Route::get('{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
            Route::put('{coupon}/validate', [CouponController::class, 'validateCoupon'])->name('update.validate');
            Route::put('{coupon}', [CouponController::class, 'update'])->name('update');
            Route::delete('{coupon}', [CouponController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('flash-sales')->name('flash-sales.')->group(function (): void {
            Route::get('/', [FlashSaleController::class, 'index'])->name('index');
            Route::get('data', [FlashSaleController::class, 'data'])->name('data');
            Route::get('search-products', [FlashSaleController::class, 'searchProducts'])->name('search-products');
            Route::get('create', [FlashSaleController::class, 'create'])->name('create');
            Route::post('validate', [FlashSaleController::class, 'validateFlashSale'])->name('validate');
            Route::patch('{flash_sale}/toggle-active', [FlashSaleController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [FlashSaleController::class, 'store'])->name('store');
            Route::get('{flash_sale}/edit', [FlashSaleController::class, 'edit'])->name('edit');
            Route::put('{flash_sale}/validate', [FlashSaleController::class, 'validateFlashSale'])->name('update.validate');
            Route::put('{flash_sale}', [FlashSaleController::class, 'update'])->name('update');
            Route::delete('{flash_sale}', [FlashSaleController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('search-suggestions')->name('search-suggestions.')->group(function (): void {
            Route::get('/', [SearchSuggestionController::class, 'index'])->name('index');
            Route::get('create', [SearchSuggestionController::class, 'create'])->name('create');
            Route::post('import', [SearchSuggestionController::class, 'import'])->name('import');
            Route::post('bulk-update', [SearchSuggestionController::class, 'bulkUpdate'])->name('bulk-update');
            Route::post('/', [SearchSuggestionController::class, 'store'])->name('store');
            Route::get('{search_suggestion}/edit', [SearchSuggestionController::class, 'edit'])->name('edit');
            Route::put('{search_suggestion}', [SearchSuggestionController::class, 'update'])->name('update');
            Route::delete('{search_suggestion}', [SearchSuggestionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('warehouses')->name('warehouses.')->group(function (): void {
            Route::get('/', [WarehouseController::class, 'index'])->name('index');
            Route::get('data', [WarehouseController::class, 'data'])->name('data');
            Route::get('cities-by-governorate/{governorate}', [WarehouseController::class, 'citiesByGovernorate'])->name('cities-by-governorate');
            Route::get('create', [WarehouseController::class, 'create'])->name('create');
            Route::post('validate', [WarehouseController::class, 'validateWarehouse'])->name('validate');
            Route::patch('{warehouse}/toggle-active', [WarehouseController::class, 'toggleActive'])->name('toggle-active');
            Route::patch('{warehouse}/make-default', [WarehouseController::class, 'makeDefault'])->name('make-default');
            Route::post('/', [WarehouseController::class, 'store'])->name('store');
            Route::get('{warehouse}/edit', [WarehouseController::class, 'edit'])->name('edit');
            Route::put('{warehouse}/validate', [WarehouseController::class, 'validateWarehouse'])->name('update.validate');
            Route::put('{warehouse}', [WarehouseController::class, 'update'])->name('update');
            Route::delete('{warehouse}', [WarehouseController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('stock')->name('stock.')->group(function (): void {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('data', [StockController::class, 'data'])->name('data');
            Route::get('movements', [StockController::class, 'movements'])->name('movements');
            Route::get('movements/data', [StockController::class, 'movementsData'])->name('movements.data');
            Route::get('{stock}/adjust', [StockController::class, 'adjustForm'])->name('adjust-form');
            Route::post('{stock}/adjust', [StockController::class, 'adjust'])->name('adjust');
        });

        Route::prefix('purchases')->name('purchases.')->group(function (): void {
            Route::get('/', [PurchaseController::class, 'index'])->name('index');
            Route::get('data', [PurchaseController::class, 'data'])->name('data');
            Route::get('export', [PurchaseController::class, 'export'])->name('export');
            Route::get('search-products', [PurchaseController::class, 'searchProducts'])->name('search-products');
            Route::post('quick-add-supplier', [PurchaseController::class, 'quickAddSupplier'])->name('quick-add-supplier');
            Route::get('create', [PurchaseController::class, 'create'])->name('create');
            Route::post('/', [PurchaseController::class, 'store'])->name('store');
            Route::get('{purchase}', [PurchaseController::class, 'show'])->name('show');
            Route::post('{purchase}/confirm', [PurchaseController::class, 'confirm'])->name('confirm');
            Route::delete('{purchase}', [PurchaseController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('orders')->name('orders.')->group(function (): void {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('data', [OrderController::class, 'data'])->name('data');

            Route::get('pos', [OrderController::class, 'pos'])->name('pos');
            Route::get('pos/search-products', [OrderController::class, 'posSearchProducts'])->name('pos.search-products');
            Route::get('pos/search-customers', [OrderController::class, 'posSearchCustomers'])->name('pos.search-customers');
            Route::post('pos/quick-add-customer', [OrderController::class, 'posQuickAddCustomer'])->name('pos.quick-add-customer');
            Route::post('pos/shipping-cost', [OrderController::class, 'posShippingCost'])->name('pos.shipping-cost');
            Route::post('pos', [OrderController::class, 'posStore'])->name('pos.store');

            Route::get('{order}', [OrderController::class, 'show'])->name('show');
            Route::post('{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('verify-payment');
            Route::post('{order}/reject-payment', [OrderController::class, 'rejectPayment'])->name('reject-payment');
            Route::post('{order}/assign-shipping', [OrderController::class, 'assignShipping'])->name('assign-shipping');
            Route::post('{order}/shipping-status', [OrderController::class, 'changeShippingStatus'])->name('shipping-status');
            Route::post('{order}/status', [OrderController::class, 'changeStatus'])->name('status');
            Route::post('{order}/admin-notes', [OrderController::class, 'updateAdminNotes'])->name('admin-notes');
            Route::get('{order}/invoice', [OrderController::class, 'printInvoice'])->name('invoice');
            Route::post('{order}/send-invoice', [OrderController::class, 'sendInvoice'])->name('send-invoice');
            Route::post('{order}/refund', [OrderController::class, 'createRefund'])->name('refund');
        });

        Route::prefix('customers')->name('customers.')->group(function (): void {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('data', [CustomerController::class, 'data'])->name('data');
            Route::get('{customer}', [CustomerController::class, 'show'])->name('show');
            Route::get('{customer}/orders-data', [CustomerController::class, 'ordersData'])->name('orders-data');
            Route::put('{customer}', [CustomerController::class, 'update'])->name('update');
            Route::patch('{customer}/toggle-active', [CustomerController::class, 'toggleActive'])->name('toggle-active');
        });

        Route::prefix('affiliates')->name('affiliates.')->group(function (): void {
            Route::get('/', [AffiliateController::class, 'index'])->name('index');
            Route::get('data', [AffiliateController::class, 'data'])->name('data');
            Route::get('generate-code', [AffiliateController::class, 'generateCode'])->name('generate-code');
            Route::get('search-customers', [AffiliateController::class, 'searchCustomers'])->name('search-customers');
            Route::get('create', [AffiliateController::class, 'create'])->name('create');
            Route::post('validate', [AffiliateController::class, 'validateAffiliate'])->name('validate');

            Route::prefix('payouts')->name('payouts.')->group(function (): void {
                Route::get('/', [AffiliateController::class, 'payouts'])->name('index');
                Route::get('data', [AffiliateController::class, 'payoutsData'])->name('data');
                Route::post('{payout}/mark-paid', [AffiliateController::class, 'markPayoutPaid'])->name('mark-paid');
                Route::post('{payout}/reject', [AffiliateController::class, 'rejectPayout'])->name('reject');
            });

            Route::patch('{affiliate}/toggle-active', [AffiliateController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [AffiliateController::class, 'store'])->name('store');
            Route::get('{affiliate}/edit', [AffiliateController::class, 'edit'])->name('edit');
            Route::put('{affiliate}/validate', [AffiliateController::class, 'validateAffiliate'])->name('update.validate');
            Route::put('{affiliate}', [AffiliateController::class, 'update'])->name('update');
            Route::get('{affiliate}', [AffiliateController::class, 'show'])->name('show');
            Route::get('{affiliate}/commissions-data', [AffiliateController::class, 'commissionsData'])->name('commissions-data');
            Route::post('{affiliate}/manual-commission', [AffiliateController::class, 'addManualCommission'])->name('manual-commission');
        });

        Route::prefix('refunds')->name('refunds.')->group(function (): void {
            Route::get('/', [RefundRequestController::class, 'index'])->name('index');
            Route::get('data', [RefundRequestController::class, 'data'])->name('data');
            Route::get('{refund}', [RefundRequestController::class, 'show'])->name('show');
            Route::post('{refund}/approve', [RefundRequestController::class, 'approve'])->name('approve');
            Route::post('{refund}/reject', [RefundRequestController::class, 'reject'])->name('reject');
        });

        Route::prefix('expense-categories')->name('expense-categories.')->group(function (): void {
            Route::get('/', [ExpenseCategoryController::class, 'index'])->name('index');
            Route::get('create', [ExpenseCategoryController::class, 'create'])->name('create');
            Route::post('validate', [ExpenseCategoryController::class, 'validateCategory'])->name('validate');
            Route::patch('{expense_category}/toggle-active', [ExpenseCategoryController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [ExpenseCategoryController::class, 'store'])->name('store');
            Route::get('{expense_category}/edit', [ExpenseCategoryController::class, 'edit'])->name('edit');
            Route::put('{expense_category}/validate', [ExpenseCategoryController::class, 'validateCategory'])->name('update.validate');
            Route::put('{expense_category}', [ExpenseCategoryController::class, 'update'])->name('update');
            Route::delete('{expense_category}', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('expenses')->name('expenses.')->group(function (): void {
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::get('export', [ExpenseController::class, 'export'])->name('export');
            Route::get('create', [ExpenseController::class, 'create'])->name('create');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
            Route::get('{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
            Route::put('{expense}', [ExpenseController::class, 'update'])->name('update');
            Route::delete('{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('accounting')->name('accounting.')->group(function (): void {
            Route::get('/', [AccountingController::class, 'index'])->name('index');
            Route::get('{node}', [AccountingController::class, 'show'])->name('show');
        });

        Route::prefix('transactions')->name('transactions.')->group(function (): void {
            Route::get('/', [TransactionController::class, 'index'])->name('index');
        });

        Route::prefix('opening-balances')->name('opening-balances.')->group(function (): void {
            Route::get('/', [OpeningBalanceController::class, 'index'])->name('index');
            Route::post('/', [OpeningBalanceController::class, 'store'])->name('store');
        });

        Route::prefix('reports')->name('reports.')->group(function (): void {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('export/{type}', [ReportController::class, 'export'])->name('export');
            Route::get('sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('purchases', [ReportController::class, 'purchases'])->name('purchases');
            Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
            Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
            Route::get('affiliates', [ReportController::class, 'affiliates'])->name('affiliates');
            Route::get('products', [ReportController::class, 'products'])->name('products');
            Route::get('stock', [ReportController::class, 'stock'])->name('stock');
            Route::get('customers', [ReportController::class, 'customers'])->name('customers');
        });

        Route::prefix('settings')->name('settings.')->group(function (): void {
            Route::get('/', [SettingController::class, 'general'])->name('general');
            Route::post('general', [SettingController::class, 'updateGeneral'])->name('general.update');
            Route::post('mail', [SettingController::class, 'updateMail'])->name('mail.update');
            Route::post('test-mail', [SettingController::class, 'testMail'])->name('test-mail');
            Route::post('advanced', [SettingController::class, 'updateAdvanced'])->name('advanced.update');
        });

        Route::prefix('gateways')->name('gateways.')->group(function (): void {
            Route::get('/', [GatewayController::class, 'index'])->name('index');
            Route::put('{gateway}', [GatewayController::class, 'update'])->name('update');
        });

        Route::prefix('shipping')->name('shipping.')->group(function (): void {
            Route::get('/', [ShippingCompanyController::class, 'index'])->name('index');
            Route::get('data', [ShippingCompanyController::class, 'data'])->name('data');
            Route::get('cities-by-governorate/{governorate}', [ShippingCompanyController::class, 'citiesByGovernorate'])->name('cities-by-governorate');
            Route::get('create', [ShippingCompanyController::class, 'create'])->name('create');
            Route::post('validate', [ShippingCompanyController::class, 'validateShippingCompany'])->name('validate');
            Route::patch('{shipping}/toggle-active', [ShippingCompanyController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [ShippingCompanyController::class, 'store'])->name('store');
            Route::get('{shipping}/edit', [ShippingCompanyController::class, 'edit'])->name('edit');
            Route::put('{shipping}/validate', [ShippingCompanyController::class, 'validateShippingCompany'])->name('update.validate');
            Route::put('{shipping}', [ShippingCompanyController::class, 'update'])->name('update');
            Route::delete('{shipping}', [ShippingCompanyController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('admin.can:admins.manage')->prefix('admins')->name('admins.')->group(function (): void {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('create', [AdminController::class, 'create'])->name('create');
            Route::post('/', [AdminController::class, 'store'])->name('store');
            Route::get('{admin}/edit', [AdminController::class, 'edit'])->name('edit');
            Route::put('{admin}', [AdminController::class, 'update'])->name('update');
            Route::patch('{admin}/toggle-active', [AdminController::class, 'toggleActive'])->name('toggle-active');
            Route::delete('{admin}', [AdminController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('admin.can:roles.manage')->prefix('roles')->name('roles.')->group(function (): void {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('admin.can:audit.view')->prefix('audits')->name('audits.')->group(function (): void {
            Route::get('/', [AuditController::class, 'index'])->name('index');
        });

        Route::prefix('static-pages')->name('static-pages.')->group(function (): void {
            Route::get('/', [StaticPageController::class, 'index'])->name('index');
            Route::get('create', [StaticPageController::class, 'create'])->name('create');
            Route::post('validate', [StaticPageController::class, 'validateStaticPage'])->name('validate');
            Route::patch('{static_page}/toggle-active', [StaticPageController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/', [StaticPageController::class, 'store'])->name('store');
            Route::get('{static_page}/edit', [StaticPageController::class, 'edit'])->name('edit');
            Route::put('{static_page}/validate', [StaticPageController::class, 'validateStaticPage'])->name('update.validate');
            Route::put('{static_page}', [StaticPageController::class, 'update'])->name('update');
            Route::delete('{static_page}', [StaticPageController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('seo')->name('seo.')->group(function (): void {
            Route::get('/', [SeoPageController::class, 'index'])->name('index');
            Route::post('products/bulk', [SeoPageController::class, 'bulkUpdateProducts'])->name('products.bulk');
            Route::get('page/{pageKey}/edit', [SeoPageController::class, 'editPage'])->name('page.edit');
            Route::put('page/{pageKey}', [SeoPageController::class, 'updatePage'])->name('page.update');
            Route::get('{modelType}/{modelId}/edit', [SeoPageController::class, 'editModel'])->name('model.edit');
            Route::put('{modelType}/{modelId}', [SeoPageController::class, 'updateModel'])->name('model.update');
        });

        Route::prefix('notifications')->name('notifications.')->group(function (): void {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('read-all', [NotificationController::class, 'readAll'])->name('readAll');
        });

        Route::get('lang/{locale}', function (string $locale) {
            if (in_array($locale, ['ar', 'en'], true)) {
                session(['admin_locale' => $locale]);
            }

            return back();
        })->name('lang');
    });
});
