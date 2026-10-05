<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCashierBranchAndRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Cashier Role Enforcement
        if ($user->role?->slug === 'cashier') {
            // Lock cashier to their assigned branch
            if ($user->branch_id && session('active_branch_id') !== $user->branch_id) {
                session([
                    'active_branch_id' => $user->branch_id,
                    'active_branch_name' => $user->branch?->name ?? 'Assigned Branch',
                ]);
            }

            // Check if route is in POS / Cashier whitelist
            $routeName = $request->route()?->getName();
            $allowedRoutes = [
                'pos.index',
                'pos.store',
                'pos.complete',
                'pos.update',
                'pos.remove',
                'pos.clear',
                'pos.hold',
                'pos.resume',
                'pos.delete_held',
                'pos.search_products',
                'pos.search_members',
                'pos.lookup_member',
                'pos.open_shift',
                'pos.close_shift',
                'sales.show',
                'sales.thermal',
                'sales.invoice',
                'shifts.index',
                'shifts.open',
                'shifts.close',
                'profile.edit',
                'profile.update',
                'profile.destroy',
                'logout',
                'manager.override.form',
                'manager.override.submit',
                'manager.override.revoke',
                'pos.manager_authorize',
                'branches.switch',
            ];

            if ($routeName && in_array($routeName, $allowedRoutes, true)) {
                return $next($request);
            }

            // If cashier has a valid manager override in session (valid for 15 mins)
            $overrideUntil = session('manager_authorized_until', 0);
            if ($overrideUntil > time()) {
                return $next($request);
            }

            // Otherwise, prompt for Branch Manager Authorization
            return redirect()->route('manager.override.form', [
                'intended' => $request->fullUrl(),
            ])->with('info', 'Branch Manager password authorization required to access administrative features.');
        }

        return $next($request);
    }
}
