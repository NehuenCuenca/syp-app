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
            'name' => 'Professional Laptop Stand',
            'buy_price' => 25,
            'profit_percentage' => 50,
            'sale_price' => 37,
            'current_stock' => 15,
            'min_stock_alert' => 5,
            'id_category' => 1
        ]);

        // Create 15 random products
        Product::factory(15)->create();
    }
}