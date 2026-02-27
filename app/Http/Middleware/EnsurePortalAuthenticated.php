<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if our HTTP-only cookie exists
        if (! $request->hasCookie('portal_token')) {
            return redirect()->route('portal.login');
        }

        return $next($request);
    }
}
