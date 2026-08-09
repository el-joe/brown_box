<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const array ALLOWED = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->route('lang');

        if (! in_array($lang, self::ALLOWED, true)) {
            abort(404);
        }

        app()->setLocale($lang);
        $request->session()->put('website_locale', $lang);

        return $next($request);
    }
}
