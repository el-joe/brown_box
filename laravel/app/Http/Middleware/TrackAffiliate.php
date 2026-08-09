<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackAffiliate
{
    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->query('ref');

        if ($code) {
            $request->session()->put('affiliate_ref_code', $code);
            $request->session()->put('affiliate_ref_expires_at', now()->addDays(30)->timestamp);
        } elseif ($request->session()->get('affiliate_ref_expires_at', 0) < now()->timestamp) {
            $request->session()->forget(['affiliate_ref_code', 'affiliate_ref_expires_at']);
        }

        return $next($request);
    }
}
