<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        $userRole = $request->user()->role?->slug;
        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Unauthorized access. You do not have permission to access this module.');
        }

        return $next($request);
    }
}
