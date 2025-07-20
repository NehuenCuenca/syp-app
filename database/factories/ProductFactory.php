<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $avgPrice = fake()->randomFloat(2, 10, 1000);
        $markup = fake()->randomFloat(2, 1.2, 2.0); // 20% to 100% markup

        return [
            'sku' => fake()->unique()->regexify('[A-Z]{2}[0-9]{4}'),
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'buy_price' => $avgPrice,
            'profit_percentage' => $markup,
            'sale_price' => $avgPrice * $markup,
            'current_stock' => fake()->numberBetween(0, 100),
            'min_stock_alert' => fake()->numberBetween(5, 20),
            'category' => fake()->randomElement(['Cigarrillos', 'Encendedores', 'Preservativos', 'Naipes', 'Pilas'])
        ];
    }
}