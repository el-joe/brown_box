<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale($request->session()->get('admin_locale', config('app.locale')));

        if (! auth('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = auth('admin')->user();

        if (! $admin->is_active) {
            auth('admin')->logout();
            $request->session()->invalidate();

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Your account has been deactivated.',
            ]);
        }

        if (! $request->session()->has('admin_last_login_logged')) {
            $admin->forceFill(['last_login_at' => now()])->saveQuietly();
            $request->session()->put('admin_last_login_logged', true);
        }

        return $next($request);
    }
}
