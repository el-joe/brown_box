<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('customer')->check()) {
            return redirect()->route('web.account.login', [
                'lang' => $request->route('lang'),
                'redirect' => $request->path(),
            ]);
        }

        $customer = auth('customer')->user();

        if (! $customer->is_active) {
            auth('customer')->logout();
            $request->session()->invalidate();

            return redirect()->route('web.account.login', ['lang' => $request->route('lang')])->withErrors([
                'email' => __('website.invalid_credentials'),
            ]);
        }

        return $next($request);
    }
}
