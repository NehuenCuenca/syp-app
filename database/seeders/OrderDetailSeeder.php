<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderDetailSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first order (typical sale order for Acme Corporation)
        $firstOrder = Order::first();
        
        // Get a product for the typical order detail
        $product = Product::first();
        
        // Create a typical order detail
        OrderDetail::factory()
            ->forOrder($firstOrder)
            ->forProduct($product, 5)
            ->create();

        // Create 5 additional random order details
        OrderDetail::factory()
            ->count(5)
            ->create();
    }
}