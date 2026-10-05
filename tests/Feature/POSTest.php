<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Member;
use App\Models\MembershipType;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
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

        $response = $this->actingAs($user)
            ->withSession(['pos_cart' => [$product->id => 2]])
            ->post('/pos/complete', ['paid_amount' => 1040]);

        $sale = Sale::query()->first();

        $this->assertNotNull($sale);
        $response->assertRedirect(route('sales.show', $sale));
        $this->assertSame('completed', $sale->status);
        $this->assertSame(1040.0, (float) $sale->grand_total);
        $this->assertSame(1040.0, (float) $sale->paid_amount);
        $this->assertSame(0.0, (float) $sale->due_amount);

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

    public function test_membership_number_is_attached_to_the_sale_and_loyalty_points_earned(): void
    {
        $user = User::factory()->create();
        $customer = Customer::query()->create([
            'customer_id' => 'CUS-001',
            'name' => 'Member Customer',
            'status' => 'active',
        ]);
        $membershipType = MembershipType::query()->create([
            'name' => 'Gold Tier',
            'discount_percentage' => 10, // 10% discount
            'reward_points' => 1,
            'is_active' => true,
        ]);
        $member = Member::query()->create([
            'membership_id' => 'MEM-001',
            'member_number' => 'M-1001',
            'customer_id' => $customer->id,
            'membership_type_id' => $membershipType->id,
            'name' => 'Member Customer',
            'join_date' => now()->toDateString(),
            'status' => 'active',
            'points' => 0,
        ]);
        $product = Product::query()->create([
            'sku' => 'P004',
            'name' => 'Tea granules',
            'slug' => 'tea-granules',
            'selling_price' => 100,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        // Cart with 2 tea items = ৳200, 10% discount = ৳20, Grand Total = ৳180 -> earns 1 point
        $response = $this->actingAs($user)
            ->withSession(['pos_cart' => [$product->id => 2]])
            ->post('/pos/complete', ['paid_amount' => 180, 'member_number' => 'M-1001']);

        $sale = Sale::query()->first();
        $this->assertNotNull($sale);
        $response->assertRedirect(route('sales.show', $sale));

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'member_id' => $member->id,
            'customer_id' => $customer->id,
            'discount_amount' => 20,
            'grand_total' => 180,
        ]);

        // Check member points updated
        $this->assertDatabaseHas('membership_point_logs', [
            'member_id' => $member->id,
            'type' => 'earned',
            'points' => 1,
        ]);
    }

    public function test_sale_can_be_completed_with_json_encoded_payments_string(): void
    {
        $user = User::factory()->create();
        $product = Product::query()->create([
            'sku' => 'P100',
            'name' => 'Pran Frooto Mango Juice',
            'slug' => 'pran-frooto-mango-juice',
            'selling_price' => 110,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        $paymentsJson = json_encode([
            ['method' => 'cash', 'amount' => 1000]
        ]);

        $response = $this->actingAs($user)
            ->withSession(['pos_cart' => [$product->id => 1]])
            ->post('/pos/complete', [
                'payments' => $paymentsJson,
            ]);

        $sale = Sale::query()->latest('id')->first();
        $this->assertNotNull($sale);
        $response->assertRedirect(route('sales.show', $sale));

        $this->assertSame(110.0, (float) $sale->grand_total);
        $this->assertSame(110.0, (float) $sale->paid_amount);
        $this->assertSame(890.0, (float) $sale->change_amount);
        $this->assertSame(0.0, (float) $sale->due_amount);
    }

    public function test_loyalty_points_can_be_redeemed_for_discount_per_100_points_150_taka(): void
    {
        \App\Models\Setting::query()->updateOrCreate(['key' => 'loyalty_discount_per_hundred_points'], ['value' => '150']);

        $user = User::factory()->create();
        $customer = Customer::query()->create([
            'customer_id' => 'CUS-POINTS-01',
            'name' => 'Loyalty Member',
            'status' => 'active',
        ]);
        $membershipType = MembershipType::query()->create([
            'name' => 'Standard',
            'discount_percentage' => 0,
            'reward_points' => 1,
            'is_active' => true,
        ]);
        $member = Member::query()->create([
            'membership_id' => 'MEM-POINTS-01',
            'member_number' => 'M-POINTS-100',
            'customer_id' => $customer->id,
            'membership_type_id' => $membershipType->id,
            'name' => 'Loyalty Member',
            'join_date' => now()->toDateString(),
            'status' => 'active',
            'points' => 250, // Has 250 points
        ]);

        $product = Product::query()->create([
            'sku' => 'P-REDEEM-01',
            'name' => 'Premium Olive Oil',
            'slug' => 'premium-olive-oil',
            'selling_price' => 800,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        // Cart total ৳800, redeem 100 points -> ৳150 discount -> Grand Total ৳650
        $response = $this->actingAs($user)
            ->withSession(['pos_cart' => [$product->id => 1]])
            ->post('/pos/complete', [
                'member_number' => 'M-POINTS-100',
                'points_redeemed' => 100,
                'paid_amount' => 650,
            ]);

        $sale = Sale::query()->latest('id')->first();
        $this->assertNotNull($sale);
        $response->assertRedirect(route('sales.show', $sale));

        $this->assertEquals(100, $sale->points_redeemed);
        $this->assertEquals(150.0, (float) $sale->points_discount);
        $this->assertEquals(150.0, (float) $sale->discount_amount);
        $this->assertEquals(650.0, (float) $sale->grand_total);
        $this->assertEquals(650.0, (float) $sale->paid_amount);

        // Member balance should be 250 - 100 = 150 points + points earned on ৳650 spend (6 points) = 156 points
        $member->refresh();
        $this->assertDatabaseHas('membership_point_logs', [
            'member_id' => $member->id,
            'type' => 'redeemed',
            'points' => -100,
        ]);
    }
}
