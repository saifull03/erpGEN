<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseProductStock;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Branches
        $branches = [
            [
                'name' => 'Main Headquarters & Superstore',
                'code' => 'BR-HQ01',
                'phone' => '+8801711000101',
                'email' => 'hq@onestop.local',
                'address' => 'Tejgaon Commercial Area, Dhaka-1208',
                'is_main' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Dhanmondi Flagship Store',
                'code' => 'BR-DHM02',
                'phone' => '+8801711000102',
                'email' => 'dhanmondi@onestop.local',
                'address' => 'Road 27 (Old), Dhanmondi, Dhaka-1209',
                'is_main' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Gulshan 2 Supercenter',
                'code' => 'BR-GLS03',
                'phone' => '+8801711000103',
                'email' => 'gulshan@onestop.local',
                'address' => 'Avenue 2, Gulshan-2 Circle, Dhaka-1212',
                'is_main' => false,
                'status' => 'active',
            ],
            [
                'name' => 'Banani Express Mart',
                'code' => 'BR-BNI04',
                'phone' => '+8801711000104',
                'email' => 'banani@onestop.local',
                'address' => 'Kemal Ataturk Avenue, Banani, Dhaka-1213',
                'is_main' => false,
                'status' => 'active',
            ],
        ];

        $createdBranches = [];
        foreach ($branches as $b) {
            $createdBranches[$b['code']] = Branch::query()->updateOrCreate(['code' => $b['code']], $b);
        }

        // 2. Create Warehouses
        $warehouses = [
            [
                'branch_id' => $createdBranches['BR-HQ01']->id,
                'name' => 'Central Logistics & Distribution Hub',
                'code' => 'WH-CDH01',
                'phone' => '+8801711000201',
                'address' => 'Plot 42, Tejgaon I/A, Dhaka',
                'is_primary' => true,
                'status' => 'active',
            ],
            [
                'branch_id' => $createdBranches['BR-HQ01']->id,
                'name' => 'HQ Ground Floor Retail Stockroom',
                'code' => 'WH-HQ01',
                'phone' => '+8801711000202',
                'address' => 'Tejgaon Showroom Backroom',
                'is_primary' => false,
                'status' => 'active',
            ],
            [
                'branch_id' => $createdBranches['BR-DHM02']->id,
                'name' => 'Dhanmondi Branch Warehouse',
                'code' => 'WH-DHM01',
                'phone' => '+8801711000203',
                'address' => 'Road 27 Dhanmondi Basement Storage',
                'is_primary' => true,
                'status' => 'active',
            ],
            [
                'branch_id' => $createdBranches['BR-GLS03']->id,
                'name' => 'Gulshan Supercenter Storage',
                'code' => 'WH-GLS01',
                'phone' => '+8801711000204',
                'address' => 'Gulshan-2 Basement Floor',
                'is_primary' => true,
                'status' => 'active',
            ],
            [
                'branch_id' => $createdBranches['BR-BNI04']->id,
                'name' => 'Banani Express Stock Room',
                'code' => 'WH-BNI01',
                'phone' => '+8801711000205',
                'address' => 'Banani Commercial Area 2nd Floor',
                'is_primary' => true,
                'status' => 'active',
            ],
        ];

        $createdWarehouses = [];
        foreach ($warehouses as $w) {
            $createdWarehouses[$w['code']] = Warehouse::query()->updateOrCreate(['code' => $w['code']], $w);
        }

        // 3. Assign Default Branches to Users
        $hqBranch = $createdBranches['BR-HQ01'];
        $dhmBranch = $createdBranches['BR-DHM02'];

        User::query()->whereNull('branch_id')->update(['branch_id' => $hqBranch->id]);

        $cashier = User::query()->where('email', 'cashier@onestop.local')->first();
        if ($cashier) {
            $cashier->update(['branch_id' => $dhmBranch->id]);
        }

        // 4. Distribute Inventory Stock into WarehouseProductStock across warehouses
        $centralWh = $createdWarehouses['WH-CDH01'];
        $dhmWh = $createdWarehouses['WH-DHM01'];
        $glsWh = $createdWarehouses['WH-GLS01'];
        $bniWh = $createdWarehouses['WH-BNI01'];

        $products = Product::query()->get();
        $stockRecords = [];

        foreach ($products as $prd) {
            $totalStock = $prd->current_stock;

            // Split total stock across warehouses (50% Central Hub, 20% Dhanmondi, 20% Gulshan, 10% Banani)
            $centralStock = (int) round($totalStock * 0.50);
            $dhmStock = (int) round($totalStock * 0.20);
            $glsStock = (int) round($totalStock * 0.20);
            $bniStock = max(0, $totalStock - ($centralStock + $dhmStock + $glsStock));

            $stockRecords[] = [
                'warehouse_id' => $centralWh->id,
                'product_id' => $prd->id,
                'stock' => $centralStock,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $stockRecords[] = [
                'warehouse_id' => $dhmWh->id,
                'product_id' => $prd->id,
                'stock' => $dhmStock,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $stockRecords[] = [
                'warehouse_id' => $glsWh->id,
                'product_id' => $prd->id,
                'stock' => $glsStock,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $stockRecords[] = [
                'warehouse_id' => $bniWh->id,
                'product_id' => $prd->id,
                'stock' => $bniStock,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach (array_chunk($stockRecords, 200) as $chunk) {
            WarehouseProductStock::query()->upsert(
                $chunk,
                ['warehouse_id', 'product_id'],
                ['stock', 'updated_at']
            );
        }
    }
}
