<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    protected $model = OrderDetail::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $quantity = fake()->numberBetween(1, 10);
        $unit_price =  fake()->numberBetween(500, 2000);
        $percentage_applied = fake()->numberBetween(15, 120);
        $line_subtotal = (int)($quantity * $unit_price) * ($percentage_applied / 100);
        
        return [
            'order_id' => Order::factory(),
            'product_id' => $product ? $product->id : Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'percentage_applied' => $percentage_applied,
            'line_subtotal' => $line_subtotal
        ];
    }

    public function forOrder(Order $order): Factory
    {
        return $this->state(function (array $attributes) use ($order) {
            return ['order_id' => $order->id];
        });
    }

    public function forProduct(Product $product, $quantity = null): Factory
    {
        return $this->state(function (array $attributes) use ($product, $quantity) {
            $qty = $quantity ?? fake()->numberBetween(1, 10);
            $unit_price = $product->sale_price;
            $percentage_applied = fake()->numberBetween(10, 120);
            $line_subtotal = 0;

            return [
                'product_id' => $product->id,
                'quantity' => $qty,
                'unit_price' => $unit_price,
                'percentage_applied' => $percentage_applied,
                'line_subtotal' => $line_subtotal
            ];
        });
    }
}