<?php

namespace App\Http\Controllers\Affiliate\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('affiliate')->check()) {
            return redirect()->route('affiliate.dashboard');
        }

        return view('affiliate.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::guard('affiliate')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $customer = Auth::guard('affiliate')->user();

        if (! $customer->affiliate || ! $customer->affiliate->is_active) {
            Auth::guard('affiliate')->logout();

            throw ValidationException::withMessages([
                'email' => __('This account does not have an active affiliate profile.'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('affiliate.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('affiliate')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('affiliate.login');
    }
}
