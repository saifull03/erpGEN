<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class POSTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_complete_a_sale_from_the_pos_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::query()->create([
            'sku' => 'P001',
            'name' => 'Rice 5kg',
            'slug' => 'rice-5kg',
            'purchase_price' => 450,
            'selling_price' => 520,
            'current_stock' => 20,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->withSession(['pos_cart' => [$product->id => 2]])
            ->post('/pos/complete', ['paid_amount' => 1040])
            ->assertRedirect('/sales');

        $sale = Sale::query()->first();

        $this->assertNotNull($sale);
        $this->assertSame('completed', $sale->status);
        $this->assertSame(1040.0, (float) $sale->grand_total);
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_stock' => 18,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => -2,
            'new_stock' => 18,
        ]);
    }

    public function test_a_user_can_update_remove_and_clear_the_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::query()->create([
            'sku' => 'P002',
            'name' => 'Cooking Oil 1L',
            'slug' => 'cooking-oil-1l',
            'selling_price' => 190,
            'current_stock' => 15,
            'status' => 'active',
        ]);

        $this->actingAs($user)->withSession(['pos_cart' => [$product->id => 1]]);
        $this->patch("/pos/{$product->id}", ['quantity' => 3])
            ->assertRedirect('/pos')
            ->assertSessionHas('pos_cart', [$product->id => 3]);

        $this->delete("/pos/{$product->id}")
            ->assertRedirect('/pos')
            ->assertSessionHas('pos_cart', []);
    }

    public function test_overpayment_is_rejected_without_creating_a_sale(): void
    {
        $user = User::factory()->create();
        $product = Product::query()->create([
            'sku' => 'P003',
            'name' => 'Sugar 1kg',
            'slug' => 'sugar-1kg',
            'selling_price' => 100,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['pos_cart' => [$product->id => 1]])
            ->post('/pos/complete', ['paid_amount' => 101]);

        $response->assertSessionHasErrors('paid_amount');
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'current_stock' => 10]);
    }

    public function test_membership_number_is_attached_to_the_sale(): void
    {
        $user = User::factory()->create();
        $customerId = DB::table('customers')->insertGetId([
            'customer_id' => 'CUS-001',
            'name' => 'Member Customer',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $membershipTypeId = DB::table('membership_types')->insertGetId([
            'name' => 'Standard',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $memberId = DB::table('members')->insertGetId([
            'membership_id' => 'MEM-001',
            'member_number' => 'M-1001',
            'customer_id' => $customerId,
            'membership_type_id' => $membershipTypeId,
            'name' => 'Member Customer',
            'join_date' => now()->toDateString(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $product = Product::query()->create([
            'sku' => 'P004',
            'name' => 'Tea  granules',
            'slug' => 'tea-granules',
            'selling_price' => 80,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->withSession(['pos_cart' => [$product->id => 1]])
            ->post('/pos/complete', ['paid_amount' => 80, 'member_number' => 'M-1001'])
            ->assertRedirect('/sales');

        $this->assertDatabaseHas('sales', [
            'member_id' => $memberId,
            'customer_id' => $customerId,
        ]);
    }
}
