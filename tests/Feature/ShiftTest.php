<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_open_and_close_shift_with_reconciliation(): void
    {
        $user = User::factory()->create();

        // 1. Open Shift with ৳500 float
        $this->actingAs($user)->post('/shifts/open', [
            'opening_balance' => 500,
            'notes' => 'Counter 1 Morning',
        ])->assertSessionHas('success');

        $shift = CashRegister::query()->where('user_id', $user->id)->where('status', 'open')->first();
        $this->assertNotNull($shift);
        $this->assertSame(500.0, (float) $shift->opening_balance);
        $this->assertSame(500.0, (float) $shift->expected_balance);

        // 2. Perform a cash sale of ৳300
        $product = Product::query()->create([
            'sku' => 'P-SHIFT-01',
            'name' => 'Biscuits Box',
            'slug' => 'biscuits-box',
            'selling_price' => 300,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->withSession(['pos_cart' => [$product->id => 1]])
            ->post('/pos/complete', ['paid_amount' => 300, 'payment_method' => 'cash']);

        $shift->refresh();
        $this->assertSame(300.0, (float) $shift->cash_sales);
        $this->assertSame(800.0, (float) $shift->expected_balance);

        // 3. Close Shift with counted ৳800
        $this->actingAs($user)->post("/shifts/{$shift->id}/close", [
            'actual_balance' => 800,
            'notes' => 'Balanced perfectly',
        ])->assertSessionHas('success');

        $shift->refresh();
        $this->assertSame('closed', $shift->status);
        $this->assertSame(800.0, (float) $shift->actual_balance);
        $this->assertSame(0.0, (float) $shift->difference);
    }
}
