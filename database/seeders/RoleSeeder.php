<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full access across the whole ERP system.'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Operations management across sales, inventory and staff.'],
            ['name' => 'Cashier', 'slug' => 'cashier', 'description' => 'POS and sales operations only.'],
            ['name' => 'Inventory Manager', 'slug' => 'inventory-manager', 'description' => 'Inventory, products and stock adjustments.'],
            ['name' => 'Accountant', 'slug' => 'accountant', 'description' => 'Financial and accounting reports.'],
        ];

        foreach ($roles as $role) {
            Role::query()->firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
