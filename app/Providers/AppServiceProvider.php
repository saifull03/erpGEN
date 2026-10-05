<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Super Admin bypass: Grant access to all Gate and Policy abilities
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        Gate::define('manage-products', fn ($user) => in_array($user->role?->slug, ['super-admin', 'admin', 'manager', 'inventory-manager'], true));
        Gate::define('manage-sales', fn ($user) => in_array($user->role?->slug, ['super-admin', 'admin', 'manager', 'cashier'], true));
        Gate::define('manage-finance', fn ($user) => in_array($user->role?->slug, ['super-admin', 'admin', 'manager', 'accountant'], true));
    }
}

