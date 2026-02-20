<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        // Get first product for the typical movement
        $product = Product::first() ?? Product::factory()->create();

        // Create a typical purchase movement
        StockMovement::create([
            'product_id' => $product->id,
            'id_order' => 1, // Initial stock movement
            'id_order_detail' => 1, // Initial stock movement
            'movement_type_id' => 1,
            'quantity_moved' => 100,
            'notes' => 'Initial stock purchase for ' . $product->name
        ]);

        // Create 10 random stock movements
        StockMovement::factory()->count(10)->create();
    }
}