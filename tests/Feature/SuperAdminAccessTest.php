<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class SuperAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected Branch $mainBranch;
    protected Branch $dhanmondiBranch;

    protected function setUp(): void
    {
        parent::setUp();

        $superAdminRole = Role::firstOrCreate(['slug' => 'super-admin'], ['name' => 'Super Administrator']);
        Role::firstOrCreate(['slug' => 'cashier'], ['name' => 'Cashier']);
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Branch Manager']);

        $this->mainBranch = Branch::firstOrCreate(
            ['code' => 'HQ-01'],
            ['name' => 'Main Outlet (HQ)', 'is_main' => true, 'status' => 'active']
        );

        $this->dhanmondiBranch = Branch::firstOrCreate(
            ['code' => 'DMD-01'],
            ['name' => 'Dhanmondi Express', 'is_main' => false, 'status' => 'active']
        );

        $this->superAdmin = User::factory()->create([
            'name' => 'Global Super Administrator',
            'email' => 'superadmin@onestop.com',
            'role_id' => $superAdminRole->id,
            'branch_id' => $this->mainBranch->id,
            'status' => 'active',
        ]);
    }

    public function test_super_admin_can_access_all_core_routes(): void
    {
        $routes = [
            'dashboard',
            'pos.index',
            'products.index',
            'categories.index',
            'brands.index',
            'units.index',
            'inventory.index',
            'inventory.alerts',
            'warehouses.index',
            'transfers.index',
            'sales.index',
            'purchases.index',
            'members.index',
            'customers.index',
            'suppliers.index',
            'expenses.index',
            'accounts.ledger',
            'shifts.index',
            'reports.index',
            'branches.index',
            'users.index',
            'audit_logs.index',
            'settings.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->superAdmin)->get(route($route));
            $response->assertStatus(200);
        }
    }

    public function test_super_admin_can_switch_to_any_branch_or_consolidated_hq(): void
    {
        // Switch to specific branch
        $response = $this->actingAs($this->superAdmin)->post(route('branches.switch'), [
            'branch_id' => $this->dhanmondiBranch->id,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('active_branch_id', $this->dhanmondiBranch->id);

        // Switch back to all outlets (Consolidated HQ)
        $response = $this->actingAs($this->superAdmin)->post(route('branches.switch'), [
            'branch_id' => 'all',
        ]);
        $response->assertRedirect();
        $response->assertSessionMissing('active_branch_id');
    }

    public function test_super_admin_passes_all_gates_and_role_checks(): void
    {
        $this->assertTrue($this->superAdmin->isSuperAdmin());
        $this->assertTrue($this->superAdmin->hasRole('admin'));
        $this->assertTrue($this->superAdmin->hasRole('cashier'));
        $this->assertTrue($this->superAdmin->hasRole('manager'));
        $this->assertTrue($this->superAdmin->hasPermission('manage-everything'));

        $this->actingAs($this->superAdmin);
        $this->assertTrue(Gate::allows('manage-finance'));
        $this->assertTrue(Gate::allows('any-arbitrary-ability'));
    }

    public function test_super_admin_can_edit_and_update_staff_member(): void
    {
        $cashierRole = Role::where('slug', 'cashier')->first();
        $adminRole = Role::where('slug', 'admin')->first();

        $staff = User::factory()->create([
            'name' => 'Original Staff Name',
            'email' => 'originalstaff@onestop.local',
            'phone' => '01711111111',
            'role_id' => $cashierRole->id,
            'branch_id' => $this->mainBranch->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)->put(route('users.update', $staff), [
            'name' => 'Updated Staff Name',
            'email' => 'updatedstaff@onestop.local',
            'phone' => '01722222222',
            'role_id' => $adminRole->id,
            'branch_id' => $this->dhanmondiBranch->id,
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $staff->refresh();
        $this->assertEquals('Updated Staff Name', $staff->name);
        $this->assertEquals('updatedstaff@onestop.local', $staff->email);
        $this->assertEquals('01722222222', $staff->phone);
        $this->assertEquals($adminRole->id, $staff->role_id);
        $this->assertEquals($this->dhanmondiBranch->id, $staff->branch_id);
        $this->assertEquals('inactive', $staff->status);
    }

    public function test_super_admin_can_perform_all_branch_manager_operations(): void
    {
        // 1. Shift Opening
        $response = $this->actingAs($this->superAdmin)->post(route('shifts.open'), [
            'opening_balance' => 5000,
            'notes' => 'Super Admin float',
        ]);
        $response->assertRedirect();

        // 2. Expense Recording
        $response = $this->actingAs($this->superAdmin)->post(route('expenses.store'), [
            'category' => 'Utilities',
            'description' => 'Store electricity and generator bill',
            'amount' => 1200,
            'date' => now()->toDateString(),
        ]);
        $response->assertRedirect(route('expenses.index'));

        // 3. Inventory Adjustment
        $product = Product::create([
            'sku' => 'TEST-SKU-999',
            'barcode' => '894123456789',
            'name' => 'Test Product',
            'slug' => 'test-product',
            'purchase_price' => 50,
            'selling_price' => 75,
            'current_stock' => 50,
            'minimum_stock' => 5,
        ]);
        $response = $this->actingAs($this->superAdmin)->post(route('inventory.adjust'), [
            'product_id' => $product->id,
            'new_stock' => 60,
            'type' => 'adjustment',
            'reason' => 'Manager restock adjustment',
        ]);
        $response->assertRedirect(route('inventory.index'));
        $product->refresh();
        $this->assertEquals(60, $product->current_stock);
    }
}


