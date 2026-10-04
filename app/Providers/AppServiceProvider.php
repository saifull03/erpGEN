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
        Gate::define('manage-products', fn ($user) => in_array($user->role?->slug, ['super-admin', 'manager', 'inventory-manager'], true));
        Gate::define('manage-sales', fn ($user) => in_array($user->role?->slug, ['super-admin', 'manager', 'cashier'], true));
        Gate::define('manage-finance', fn ($user) => in_array($user->role?->slug, ['super-admin', 'manager', 'accountant'], true));
    }
}
