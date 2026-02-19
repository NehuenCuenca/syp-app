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
        $avgPrice = fake()->numberBetween(10, 1000);
        $markup = fake()->numberBetween(15, 120);
        $totalCategories = Category::count();

        return [
            'name' => fake()->words(3, true),
            'buy_price' => $avgPrice,
            'profit_percentage' => $markup,
            'sale_price' => (int)($avgPrice * $markup),
            'current_stock' => fake()->numberBetween(0, 100),
            'min_stock_alert' => fake()->numberBetween(5, 20),
            'category_id' => fake()->numberBetween(1, $totalCategories),
        ];
    }
}