<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create a typical product
        Product::create([
            'sku' => 'LP1001',
            'name' => 'Professional Laptop Stand',
            'description' => 'Ergonomic aluminum laptop stand with adjustable height and angle. Compatible with all laptop sizes.',
            'avg_purchase_price' => 25.00,
            'suggested_sale_price' => 49.99,
            'current_stock' => 15,
            'min_stock_alert' => 5,
            'category' => 'Pilas'
        ]);

        // Create 15 random products
        Product::factory(15)->create();
    }
}