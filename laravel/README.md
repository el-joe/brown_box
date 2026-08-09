# Brown Box

Brown Box is a bilingual (Arabic/English) e-commerce platform built on Laravel, covering three
front-ends from a single codebase:

- **Website** — the public storefront (`routes/website.php`), locale-prefixed (`/en/...`, `/ar/...`).
- **Admin panel** — order, catalog, accounting and affiliate management (`routes/admin.php`).
- **Affiliate panel** — referral dashboard, commissions and payouts (`routes/affiliate.php`).

## Architecture decisions

- **Repository + Service layers.** Controllers depend on service classes (`app/Services`), which
  depend on repository interfaces (`app/Repositories/Contracts`) bound in
  `AppServiceProvider`/`RepositoryServiceProvider`. Controllers never touch Eloquent directly for
  write operations, keeping business rules (stock deduction, commission calculation, accounting
  entries) in one place and unit-testable in isolation from HTTP.
- **Guards per panel.** Three separate auth guards (`admin`, `affiliate`, customer/web) back three
  independent login flows, enforced by `AdminAuthenticated`, `AffiliateAuthenticated` and
  `CustomerAuthenticated` middleware.
- **Locale-first routing.** All storefront routes are nested under a `{lang}` segment validated by
  `SetLocale` middleware; `current_lang()` and `is_rtl()` helpers drive both Blade templates and
  the compiled RTL/LTR CSS bundles (see below).
- **Domain events + jobs.** Order lifecycle and affiliate actions dispatch events
  (`OrderCreated`, `OrderStatusChanged`, ...) consumed by queued jobs/listeners for email
  notifications, invoice generation, and commission processing, keeping request/response cycles
  fast.
- **Money and identifiers.** All monetary amounts are stored/rounded consistently through
  `money_format()`; order/invoice numbers are generated via `generate_order_number()` /
  `generate_invoice_number()` in `app/Helpers/helpers.php` (format `ORD-YYYYMMDD-NNNNN`).

## Requirements

- PHP 8.3+
- Composer 2.x
- Node.js 18+ and npm
- MySQL/MariaDB (SQLite is also supported for local/dev use)

## Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Environment
cp .env.example .env
php artisan key:generate

# Edit .env: DB_*, MAIL_*, APP_URL, QUEUE_CONNECTION=database

# 4. Database
php artisan migrate
php artisan db:seed

# 5. Storage symlink (for product images, payment proofs, invoices, etc.)
php artisan storage:link

# 6. Build front-end assets
npm run build      # production
npm run dev         # local development (Vite dev server)
```

The seeder set includes an admin user, roles/permissions (`spatie/laravel-permission`) and default
static pages — check `database/seeders/DatabaseSeeder.php` for the full list and adjust
credentials before deploying.

## Queue worker

Background jobs (order emails, invoice PDFs, commission approval, stock digests, coupon/flash-sale
cleanup) run on the `database` queue connection by default. Start a worker locally with:

```bash
php artisan queue:work --tries=3
```

In production, run the worker under a process supervisor (Supervisor, systemd, or Laravel Horizon
if you switch to Redis) so it restarts automatically. After deploying code changes, restart workers
with:

```bash
php artisan queue:restart
```

Scheduled tasks (stock alerts, commission approval, coupon/flash-sale cleanup, sitemap generation —
see `routes/console.php`) require the scheduler cron entry:

```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## Caching

Settings, the active category tree, active flash sales and homepage product lists are cached via
`Cache::remember()` (see `app/Http/Controllers/Website/HomeController.php` and
`app/Helpers/helpers.php`). Catalog writes (`Product`, `Category`, `FlashSale`) automatically flush
the relevant keys through `App\Observers\CatalogCacheObserver`. If you switch `CACHE_STORE` to a
tag-capable driver (Redis/Memcached), this is also where you'd introduce `Cache::tags()`.

## RTL / LTR assets

Each panel compiles two CSS entry points — `app.ltr.css` and `app.rtl.css` (see
`resources/css/{admin,website}/`) — built from the same Tailwind source with a small set of
direction-specific overrides for third-party UI (Select2, DataTables, flatpickr). Layouts pick the
correct bundle at render time via `is_rtl()`.

## Tests

```bash
php artisan test
# or
./vendor/bin/phpunit
```

## Useful artisan commands

| Command | Purpose |
|---|---|
| `php artisan queue:work` | Process queued jobs |
| `php artisan schedule:run` | Run due scheduled tasks (invoke via cron) |
| `php artisan stock:alert` | Manually trigger low-stock digest |
| `php artisan affiliates:approve-commissions` | Manually approve ready commissions |
| `php artisan seo:sitemap` | Regenerate the sitemap |
| `php artisan storage:link` | Link `public/storage` to `storage/app/public` |
