<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseProductStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchWarehouseTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch1;
    protected Branch $branch2;
    protected Warehouse $warehouse1;
    protected Warehouse $warehouse2;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Full access',
        ]);

        $this->branch1 = Branch::create([
            'name' => 'Main HQ',
            'code' => 'BR-HQ',
            'is_main' => true,
            'status' => 'active',
        ]);

        $this->branch2 = Branch::create([
            'name' => 'Gulshan Store',
            'code' => 'BR-GLS',
            'is_main' => false,
            'status' => 'active',
        ]);

        $this->warehouse1 = Warehouse::create([
            'branch_id' => $this->branch1->id,
            'name' => 'Central Hub',
            'code' => 'WH-HUB',
            'is_primary' => true,
            'status' => 'active',
        ]);

        $this->warehouse2 = Warehouse::create([
            'branch_id' => $this->branch2->id,
            'name' => 'Gulshan Stockroom',
            'code' => 'WH-GLS',
            'is_primary' => true,
            'status' => 'active',
        ]);

        $this->admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@onestop.local',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'branch_id' => $this->branch1->id,
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'sku' => 'TEST-001',
            'barcode' => '890100000001',
            'name' => 'Test Coffee',
            'slug' => 'test-coffee',
            'purchase_price' => 100,
            'selling_price' => 120,
            'current_stock' => 50,
            'status' => 'active',
        ]);

        WarehouseProductStock::create([
            'warehouse_id' => $this->warehouse1->id,
            'product_id' => $this->product->id,
            'stock' => 50,
        ]);

        WarehouseProductStock::create([
            'warehouse_id' => $this->warehouse2->id,
            'product_id' => $this->product->id,
            'stock' => 0,
        ]);
    }

    public function test_branch_index_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->admin)->get(route('branches.index'));
        $response->assertOk();
        $response->assertSee('Main HQ');
        $response->assertSee('Gulshan Store');
    }

    public function test_user_can_switch_branch_context(): void
    {
        $response = $this->actingAs($this->admin)->post(route('branches.switch'), [
            'branch_id' => $this->branch2->id,
        ]);

        $response->assertSessionHas('active_branch_id', $this->branch2->id);
    }

    public function test_user_can_create_and_complete_stock_transfer(): void
    {
        // 1. Create transfer
        $response = $this->actingAs($this->admin)->post(route('transfers.store'), [
            'from_warehouse_id' => $this->warehouse1->id,
            'to_warehouse_id' => $this->warehouse2->id,
            'notes' => 'Test Replenishment',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 15,
                ],
            ],
        ]);

        $response->assertRedirect(route('transfers.index'));
        $transfer = StockTransfer::first();
        $this->assertNotNull($transfer);
        $this->assertEquals('pending', $transfer->status);

        // 2. Ship transfer
        $shipResponse = $this->actingAs($this->admin)->post(route('transfers.ship', $transfer));
        $shipResponse->assertSessionHas('success');
        $transfer->refresh();
        $this->assertEquals('in_transit', $transfer->status);

        $originStock = WarehouseProductStock::where('warehouse_id', $this->warehouse1->id)
            ->where('product_id', $this->product->id)
            ->value('stock');
        $this->assertEquals(35, $originStock); // 50 - 15 = 35

        // 3. Receive transfer
        $receiveResponse = $this->actingAs($this->admin)->post(route('transfers.receive', $transfer));
        $receiveResponse->assertSessionHas('success');
        $transfer->refresh();
        $this->assertEquals('received', $transfer->status);

        $destStock = WarehouseProductStock::where('warehouse_id', $this->warehouse2->id)
            ->where('product_id', $this->product->id)
            ->value('stock');
        $this->assertEquals(15, $destStock); // 0 + 15 = 15
    }
}
