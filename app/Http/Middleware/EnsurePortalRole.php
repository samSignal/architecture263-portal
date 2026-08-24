<?php

namespace App\Http\Middleware;

use App\Support\Architecture263Api;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $token = $request->cookie('portal_token');

        if (! $token) {
            return redirect()->route('portal.login');
        }

        $response = app(Architecture263Api::class)->getUser($token);

        if ($response->failed() || ! in_array($role, $response->json('roles', []), true)) {
            abort(403, 'You do not have access to this area.');
        }

        return $next($request);
    }
}
