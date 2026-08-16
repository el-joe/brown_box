<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function show(Request $request): View
    {
        $slug = $request->route('slug');

        $page = StaticPage::query()->active()->where('slug', $slug)->firstOrFail();

        return view('website.pages.show', [
            'page' => $page,
        ]);
    }

    // No dedicated Faq/FaqCategory model exists yet in this project. The FAQ
    // mockup uses a distinct accordion UI grouped by category rather than the
    // free-form HTML content used by generic static pages, so it is served
    // from a small in-code dataset here rather than through StaticPage::show().
    public function faqs(): View
    {
        $groups = [
            [
                'key' => 'orders',
                'icon' => 'fa-truck-fast',
                'title' => __('website.faq_group_orders'),
                'items' => [
                    ['q' => __('website.faq_orders_q1'), 'a' => __('website.faq_orders_a1')],
                    ['q' => __('website.faq_orders_q2'), 'a' => __('website.faq_orders_a2')],
                    ['q' => __('website.faq_orders_q3'), 'a' => __('website.faq_orders_a3')],
                    ['q' => __('website.faq_orders_q4'), 'a' => __('website.faq_orders_a4')],
                ],
            ],
            [
                'key' => 'returns',
                'icon' => 'fa-rotate-left',
                'title' => __('website.faq_group_returns'),
                'items' => [
                    ['q' => __('website.faq_returns_q1'), 'a' => __('website.faq_returns_a1')],
                    ['q' => __('website.faq_returns_q2'), 'a' => __('website.faq_returns_a2')],
                    ['q' => __('website.faq_returns_q3'), 'a' => __('website.faq_returns_a3')],
                    ['q' => __('website.faq_returns_q4'), 'a' => __('website.faq_returns_a4')],
                ],
            ],
            [
                'key' => 'payments',
                'icon' => 'fa-credit-card',
                'title' => __('website.faq_group_payments'),
                'items' => [
                    ['q' => __('website.faq_payments_q1'), 'a' => __('website.faq_payments_a1')],
                    ['q' => __('website.faq_payments_q2'), 'a' => __('website.faq_payments_a2')],
                    ['q' => __('website.faq_payments_q3'), 'a' => __('website.faq_payments_a3')],
                    ['q' => __('website.faq_payments_q4'), 'a' => __('website.faq_payments_a4')],
                ],
            ],
            [
                'key' => 'account',
                'icon' => 'fa-user',
                'title' => __('website.faq_group_account'),
                'items' => [
                    ['q' => __('website.faq_account_q1'), 'a' => __('website.faq_account_a1')],
                    ['q' => __('website.faq_account_q2'), 'a' => __('website.faq_account_a2')],
                    ['q' => __('website.faq_account_q3'), 'a' => __('website.faq_account_a3')],
                    ['q' => __('website.faq_account_q4'), 'a' => __('website.faq_account_a4')],
                ],
            ],
            [
                'key' => 'products',
                'icon' => 'fa-box-open',
                'title' => __('website.faq_group_products'),
                'items' => [
                    ['q' => __('website.faq_products_q1'), 'a' => __('website.faq_products_a1')],
                    ['q' => __('website.faq_products_q2'), 'a' => __('website.faq_products_a2')],
                    ['q' => __('website.faq_products_q3'), 'a' => __('website.faq_products_a3')],
                    ['q' => __('website.faq_products_q4'), 'a' => __('website.faq_products_a4')],
                ],
            ],
        ];

        return view('website.pages.faqs', ['groups' => $groups]);
    }
}
