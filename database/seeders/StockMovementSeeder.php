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
        // Get Admin user for the typical movement
        $Admin = User::where('email', 'Admin@example.com')->first() ?? User::factory()->create();
        
        // Get first product for the typical movement
        $product = Product::first() ?? Product::factory()->create();

        // Create a typical purchase movement
        StockMovement::create([
            'id_product' => $product->id,
            'id_order' => null, // Initial stock movement
            'id_user_responsible' => $Admin->id,
            'movement_type' => 'Compra_Entrante',
            'quantity_moved' => 100,
            'movement_date' => now(),
            'external_reference' => 'INIT-001',
            'notes' => 'Initial stock purchase for ' . $product->name
        ]);

        // Create 10 random stock movements
        StockMovement::factory()
            ->count(10)
            ->create([
                'id_user_responsible' => $Admin->id // All movements created by Admin
            ]);
    }
}