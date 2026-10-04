<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_process_a_sales_return(): void
    {
        $user = User::factory()->create();
        $product = Product::query()->create([
            'sku' => 'P-RETURN-01',
            'name' => 'Milk 1L',
            'slug' => 'milk-1l',
            'selling_price' => 90,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        $sale = Sale::query()->create([
            'invoice_number' => 'INV-TEST-001',
            'user_id' => $user->id,
            'subtotal' => 180,
            'grand_total' => 180,
            'paid_amount' => 180,
            'due_amount' => 0,
            'status' => 'completed',
        ]);

        $saleItem = SaleItem::query()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 90,
            'subtotal' => 180,
            'total_price' => 180,
        ]);

        $response = $this->actingAs($user)->post("/sales/{$sale->id}/return", [
            'reason' => 'Damaged carton',
            'payment_method' => 'cash',
            'items' => [
                $saleItem->id => 1,
            ],
        ]);

        $response->assertSessionHas('success');

        // Stock restored from 10 to 11
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_stock' => 11,
        ]);

        // Sale return recorded
        $this->assertDatabaseHas('sale_returns', [
            'sale_id' => $sale->id,
            'total_refund' => 90,
        ]);

        // Stock movement recorded as return
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'sale_return',
            'quantity' => 1,
            'new_stock' => 11,
        ]);
    }
}
