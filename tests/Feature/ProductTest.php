<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function testProduct() {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);
        $product = Product::first();

        $this->get("/api/products-paging")
        ->assertStatus(200)
        ->assertJson([
            "value" => [
                "name" => $product->name,
                "category" => [
                    "id" => $product->category->id,
                    "name" => $product->category->name,
                ],
                "price" => $product->price,
                "created_at" => $product->created_at->toJson(),
                "updated_at" => $product->updated_at->toJson(),
                
            ]
        ]); 
    }

    public function testProductPaging() {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);
        $response = $this->get("/api/products-paging");
        $response->assertStatus(200);
        $response->assertSeeText("link");
        $response->assertSeeText("meta");
        $response->assertSeeText("data");
    }

    public function testProductAddResource() {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);
        $product = Product::first();
        self::assertNotNull($product);
        $response = $this->get("/api/products-debug/$product->id");
        $response->assertSeeText("author"); 
        $response->assertJson([
            "author" => "Adi Salafudin",
            "date" => now()->toDateString(),
            "data" => [
                "id" => $product->id,
                "name" => $product->name,
                "is_expensive" => $product->price < 1000, 
                "price" => $product->price
            ]
        ]);  
        $response->assertSeeText("date");
        
    }
}
  