<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $avgPrice = fake()->randomFloat(2, 10, 1000);
        $markup = fake()->randomFloat(2, 1.2, 2.0); // 20% to 100% markup
        $totalCategories = Category::count();

        return [
            'name' => fake()->words(3, true),
            'buy_price' => $avgPrice,
            'profit_percentage' => $markup,
            'sale_price' => $avgPrice * $markup,
            'current_stock' => fake()->numberBetween(0, 100),
            'min_stock_alert' => fake()->numberBetween(5, 20),
            'id_category' => fake()->numberBetween(1, $totalCategories),
        ];
    }
}