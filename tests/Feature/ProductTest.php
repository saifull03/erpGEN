<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_a_product(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/products', [
            'sku' => 'SKU-001',
            'barcode' => '123456789',
            'name' => 'Rice 5kg',
            'purchase_price' => 80,
            'selling_price' => 110,
            'minimum_stock' => 10,
            'current_stock' => 25,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['sku' => 'SKU-001', 'name' => 'Rice 5kg']);
        $this->assertEquals(1, Product::query()->count());
    }
}
