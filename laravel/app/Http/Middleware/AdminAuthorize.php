<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthorize
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = auth('admin')->user();

        abort_if(! $admin || (! $admin->isSuperAdmin() && ! $admin->can($permission)), 403);

        return $next($request);
    }
}
