<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        $movementTypes = [
            'Purchase_In',
            'Sale_Out',
            'Positive_Adjustment',
            'Negative_Adjustment',
            'Client_Return',
            'Supplier_Return'
        ];

        $movementType = fake()->randomElement($movementTypes);
        $quantity = fake()->numberBetween(1, 100);

        // For Sale_Out and Negative_Adjustment, make quantity negative
        if (in_array($movementType, ['Sale_Out', 'Negative_Adjustment'])) {
            $quantity *= -1;
        }

        return [
            'id_product' => Product::factory(),
            'id_order' => $movementType === 'Purchase_In' || $movementType === 'Sale_Out' 
                ? Order::factory() 
                : null,
            'id_user_responsible' => User::factory(),
            'movement_type' => $movementType,
            'quantity_moved' => $quantity,
            'movement_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'external_reference' => fake()->boolean(70) 
                ? fake()->bothify('??###-####') 
                : null,
            'notes' => fake()->boolean(70) 
                ? fake()->sentence() 
                : null,
        ];
    }

    public function purchase(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'movement_type' => 'Purchase_In',
                'quantity_moved' => fake()->numberBetween(1, 100),
                'external_reference' => fake()->bothify('PO##-####')
            ];
        });
    }

    public function sale(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'movement_type' => 'Sale_Out',
                'quantity_moved' => fake()->numberBetween(-100, -1),
                'external_reference' => fake()->bothify('SO##-####')
            ];
        });
    }

    public function adjustment(bool $positive = true): Factory
    {
        return $this->state(function (array $attributes) use ($positive) {
            $quantity = fake()->numberBetween(1, 50);
            return [
                'movement_type' => $positive ? 'Positive_Adjustment' : 'Negative_Adjustment',
                'quantity_moved' => $positive ? $quantity : -$quantity,
                'external_reference' => fake()->bothify('ADJ##-####')
            ];
        });
    }
}