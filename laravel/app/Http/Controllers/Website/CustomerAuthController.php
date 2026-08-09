<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function loginForm(): View
    {
        return view('website.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('customer')->attempt($data, $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('website.invalid_credentials')])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->to($this->safeRedirectUrl($request));
    }

    public function registerForm(): View
    {
        return view('website.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $customer = Customer::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->to($this->safeRedirectUrl($request));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('web.home', ['lang' => current_lang()]);
    }

    /**
     * Resolve a safe, same-origin internal redirect target from the "redirect" input,
     * falling back to the account dashboard.
     */
    private function safeRedirectUrl(Request $request): string
    {
        $default = route('web.account.index', ['lang' => current_lang()]);
        $redirect = $request->input('redirect');

        if (! $redirect || ! is_string($redirect) || str_starts_with($redirect, '//') || parse_url($redirect, PHP_URL_HOST)) {
            return $default;
        }

        return url(ltrim($redirect, '/'));
    }
}
