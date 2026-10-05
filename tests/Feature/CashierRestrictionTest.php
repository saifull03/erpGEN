<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierRestrictionTest extends TestCase
{
    use RefreshDatabase;

    protected Branch $branch;
    protected Branch $otherBranch;
    protected Role $cashierRole;
    protected Role $adminRole;
    protected User $cashier;
    protected User $branchManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'name' => 'Dhanmondi Branch',
            'code' => 'BR-DHM',
            'is_main' => false,
            'status' => 'active',
        ]);

        $this->otherBranch = Branch::create([
            'name' => 'Gulshan Branch',
            'code' => 'BR-GLS',
            'is_main' => false,
            'status' => 'active',
        ]);

        $this->cashierRole = Role::create([
            'name' => 'Cashier',
            'slug' => 'cashier',
            'description' => 'POS Cashier',
        ]);

        $this->adminRole = Role::create([
            'name' => 'Store Manager',
            'slug' => 'admin',
            'description' => 'Branch Store Manager',
        ]);

        $this->cashier = User::create([
            'name' => 'Sadia Cashier',
            'email' => 'cashier@onestop.local',
            'password' => bcrypt('password123'),
            'role_id' => $this->cashierRole->id,
            'branch_id' => $this->branch->id,
            'status' => 'active',
        ]);

        $this->branchManager = User::create([
            'name' => 'Manager Hasan',
            'email' => 'manager.dhanmondi@onestop.local',
            'password' => bcrypt('managerpass123'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branch->id,
            'status' => 'active',
        ]);
    }

    public function test_cashier_login_redirects_directly_to_pos_terminal(): void
    {
        $response = $this->post(route('login'), [
            'email' => $this->cashier->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('pos.index'));
        $this->assertAuthenticatedAs($this->cashier);
        $this->assertEquals($this->branch->id, session('active_branch_id'));
    }

    public function test_cashier_can_access_pos_terminal(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('pos.index'));
        $response->assertOk();
    }

    public function test_cashier_cannot_switch_branches(): void
    {
        $response = $this->actingAs($this->cashier)->post(route('branches.switch'), [
            'branch_id' => $this->otherBranch->id,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_cashier_accessing_dashboard_is_redirected_to_manager_override(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('dashboard'));
        $response->assertRedirect(route('manager.override.form', ['intended' => url('/dashboard')]));
    }

    public function test_cashier_accessing_products_is_redirected_to_manager_override(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('products.index'));
        $response->assertRedirect(route('manager.override.form', ['intended' => url('/products')]));
    }

    public function test_manager_override_with_wrong_password_fails(): void
    {
        $response = $this->actingAs($this->cashier)->post(route('manager.override.submit'), [
            'manager_email' => $this->branchManager->email,
            'manager_password' => 'wrongpass',
            'intended_url' => route('dashboard'),
        ]);

        $response->assertSessionHas('error');
    }

    public function test_manager_override_with_valid_manager_password_unlocks_access(): void
    {
        $response = $this->actingAs($this->cashier)->post(route('manager.override.submit'), [
            'manager_email' => $this->branchManager->email,
            'manager_password' => 'managerpass123',
            'intended_url' => route('dashboard'),
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // Verify that subsequent request to dashboard succeeds
        $dashboardResponse = $this->actingAs($this->cashier)->get(route('dashboard'));
        $dashboardResponse->assertOk();
    }

    public function test_ajax_pos_manager_authorization_succeeds_with_correct_password(): void
    {
        $response = $this->actingAs($this->cashier)->postJson(route('pos.manager_authorize'), [
            'email' => $this->branchManager->email,
            'password' => 'managerpass123',
            'action' => 'item_void',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_cashier_completing_cash_sale_does_not_require_manager_authorization(): void
    {
        $product = \App\Models\Product::query()->create([
            'sku' => 'P-CASHIER-01',
            'name' => 'Fresh Milk 1L',
            'slug' => 'fresh-milk-1l',
            'selling_price' => 90,
            'current_stock' => 15,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->cashier)
            ->withSession(['pos_cart' => [$product->id => 1]])
            ->post(route('pos.complete'), [
                'paid_amount' => 90,
                'payment_method' => 'cash',
            ]);

        // Cashier redirects back to POS terminal ready for next customer with success message
        $response->assertRedirect(route('pos.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('last_sale_id');

        $sale = \App\Models\Sale::query()->latest('id')->first();
        $this->assertNotNull($sale);

        // Accessing thermal receipt and invoice does not prompt for manager authorization
        $thermalResponse = $this->actingAs($this->cashier)->get(route('sales.thermal', $sale));
        $thermalResponse->assertOk();

        $invoiceResponse = $this->actingAs($this->cashier)->get(route('sales.invoice', $sale));
        $invoiceResponse->assertOk();

        $showResponse = $this->actingAs($this->cashier)->get(route('sales.show', $sale));
        $showResponse->assertOk();
    }
}
