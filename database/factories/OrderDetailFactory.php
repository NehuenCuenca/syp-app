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
        $unit_price = $product ? $product->sale_price : fake()->numberBetween(10, 120);
        $discount_percentage_by_unit = fake()->numberBetween(15, 120);
        $line_subtotal = (int)($quantity * $unit_price) * ($discount_percentage_by_unit / 100);
        
        return [
            'id_order' => Order::factory(),
            'id_product' => $product ? $product->id : Product::factory(),
            'quantity' => $quantity,
            'unit_price_at_order' => $unit_price,
            'discount_percentage_by_unit' => $discount_percentage_by_unit,
            'line_subtotal' => $line_subtotal
        ];
    }

    public function forOrder(Order $order): Factory
    {
        return $this->state(function (array $attributes) use ($order) {
            return ['id_order' => $order->id];
        });
    }

    public function forProduct(Product $product, $quantity = null): Factory
    {
        return $this->state(function (array $attributes) use ($product, $quantity) {
            $qty = $quantity ?? fake()->numberBetween(1, 10);
            $unit_price = $product->sale_price;
            $discount_percentage_by_unit = fake()->numberBetween(10, 120);
            $line_subtotal = 0;

            return [
                'id_product' => $product->id,
                'quantity' => $qty,
                'unit_price_at_order' => $unit_price,
                'discount_percentage_by_unit' => $discount_percentage_by_unit,
                'line_subtotal' => $line_subtotal
            ];
        });
    }
}