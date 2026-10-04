<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_and_receive_a_purchase(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::query()->create([
            'supplier_id' => 'SUP-001',
            'company_name' => 'Meghna FMCG',
            'current_payable_balance' => 0,
            'status' => 'active',
        ]);
        $product = Product::query()->create([
            'sku' => 'P-OIL-5L',
            'name' => 'Sunflower Oil 5L',
            'slug' => 'sunflower-oil-5l',
            'purchase_price' => 800,
            'selling_price' => 950,
            'current_stock' => 10,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post('/purchases', [
            'supplier_id' => $supplier->id,
            'date' => now()->toDateString(),
            'paid' => 4000,
            'status' => 'received',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'unit_price' => 800,
                ],
            ],
        ]);

        $purchase = Purchase::query()->first();
        $this->assertNotNull($purchase);
        $response->assertRedirect(route('purchases.show', $purchase));

        // Purchase totals
        $this->assertSame(8000.0, (float) $purchase->total);
        $this->assertSame(4000.0, (float) $purchase->paid);
        $this->assertSame(4000.0, (float) $purchase->due);

        // Inventory increased by 10 (10 + 10 = 20)
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_stock' => 20,
        ]);

        // Stock movement recorded
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'purchase',
            'quantity' => 10,
            'new_stock' => 20,
        ]);

        // Supplier balance updated with due amount 4000
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'current_payable_balance' => 4000,
        ]);

        // Ledger entry recorded
        $this->assertDatabaseHas('ledger_entries', [
            'ledger_type' => 'supplier',
            'entity_id' => $supplier->id,
            'reference' => $purchase->purchase_invoice_number,
        ]);
    }
}
