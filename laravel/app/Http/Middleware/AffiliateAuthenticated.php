<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AffiliateAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('affiliate')->check()) {
            return redirect()->route('affiliate.login');
        }

        $customer = auth('affiliate')->user();
        $affiliate = $customer->affiliate;

        if (! $affiliate || ! $affiliate->is_active) {
            auth('affiliate')->logout();
            $request->session()->invalidate();

            return redirect()->route('affiliate.login')->withErrors([
                'email' => __('Your affiliate account is not active.'),
            ]);
        }

        $request->attributes->set('affiliate', $affiliate);

        return $next($request);
    }
}
