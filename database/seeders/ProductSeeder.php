<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'P001',
                'name' => 'Rice 5kg',
                'purchase_price' => 450,
                'selling_price' => 520,
                'current_stock' => 20,
            ],
            [
                'sku' => 'P002',
                'name' => 'Cooking Oil 1L',
                'purchase_price' => 160,
                'selling_price' => 190,
                'current_stock' => 15,
            ],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                [
                    ...$product,
                    'slug' => Str::slug($product['name']),
                    'status' => 'active',
                ],
            );
        }
    }
}
