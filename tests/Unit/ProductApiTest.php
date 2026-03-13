<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function test_get_products()
    {      
        $user = User::factory()->create();
        
        Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'price', 'stock', 'created_at', 'is_in_stock']
                     ]
                 ]);
    }

    /** @test */
    public function test_protected_route()
    {
        
        $response = $this->getJson('/api/products');

        $response->assertStatus(401);
    }

    /** @test */
    public function test_product_creation()
    {
        $user = User::factory()->create();

        $productData = [
            'name' => 'Test Gaming Mouse',
            'description' => 'Precision gaming mouse',
            'price' => 59.99,
            'stock' => 100,
        ];

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/products', $productData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Test Gaming Mouse']);
        
        $this->assertDatabaseHas('products', ['name' => 'Test Gaming Mouse']);
    }
}
