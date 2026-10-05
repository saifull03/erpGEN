<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full access to all modules and configurations',
            ],
            [
                'name' => 'Admin / Manager',
                'slug' => 'admin',
                'description' => 'Store management, sales, purchases, inventory, customers, members, and expenses',
            ],
            [
                'name' => 'Cashier',
                'slug' => 'cashier',
                'description' => 'POS checkout, barcode scanning, member lookup, invoices, returns, shifts',
            ],
            [
                'name' => 'Inventory Manager',
                'slug' => 'inventory-manager',
                'description' => 'Products, categories, brands, suppliers, stock movements, adjustments',
            ],
            [
                'name' => 'Accountant',
                'slug' => 'accountant',
                'description' => 'Sales reports, purchase reports, expenses, ledgers, and financial accounts',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::query()->updateOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        $permissions = [
            ['name' => 'POS Access', 'slug' => 'pos-access'],
            ['name' => 'Manage Products', 'slug' => 'manage-products'],
            ['name' => 'Manage Inventory', 'slug' => 'manage-inventory'],
            ['name' => 'Manage Purchases', 'slug' => 'manage-purchases'],
            ['name' => 'Manage Sales', 'slug' => 'manage-sales'],
            ['name' => 'Process Returns', 'slug' => 'process-returns'],
            ['name' => 'Manage Suppliers', 'slug' => 'manage-suppliers'],
            ['name' => 'Manage Customers', 'slug' => 'manage-customers'],
            ['name' => 'Manage Members', 'slug' => 'manage-members'],
            ['name' => 'Manage Expenses', 'slug' => 'manage-expenses'],
            ['name' => 'Manage Shifts', 'slug' => 'manage-shifts'],
            ['name' => 'View Financial Reports', 'slug' => 'view-financial-reports'],
            ['name' => 'Manage Users', 'slug' => 'manage-users'],
            ['name' => 'Manage Settings', 'slug' => 'manage-settings'],
            ['name' => 'View Audit Logs', 'slug' => 'view-audit-logs'],
        ];

        foreach ($permissions as $p) {
            Permission::query()->updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Attach all permissions to Super Admin and Admin roles
        $allPermissionIds = Permission::query()->pluck('id');

        $superAdminRole = Role::query()->where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->permissions()->sync($allPermissionIds);
        }

        $adminRole = Role::query()->where('slug', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync($allPermissionIds);
        }

        // Attach specific permissions to cashier
        $cashierRole = Role::query()->where('slug', 'cashier')->first();
        if ($cashierRole) {
            $cashierPermissions = Permission::query()
                ->whereIn('slug', ['pos-access', 'manage-sales', 'process-returns', 'manage-customers', 'manage-members', 'manage-shifts'])
                ->pluck('id');
            $cashierRole->permissions()->sync($cashierPermissions);
        }

        $invRole = Role::query()->where('slug', 'inventory-manager')->first();
        if ($invRole) {
            $invPermissions = Permission::query()
                ->whereIn('slug', ['manage-products', 'manage-inventory', 'manage-purchases', 'manage-suppliers'])
                ->pluck('id');
            $invRole->permissions()->sync($invPermissions);
        }

        $accRole = Role::query()->where('slug', 'accountant')->first();
        if ($accRole) {
            $accPermissions = Permission::query()
                ->whereIn('slug', ['view-financial-reports', 'manage-expenses', 'manage-sales', 'manage-purchases'])
                ->pluck('id');
            $accRole->permissions()->sync($accPermissions);
        }
    }
}

