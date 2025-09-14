<?php

namespace Database\Factories;

use App\Models\MovementType;
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
        $movementType = fake()->randomElement(MovementType::all()->pluck('name')->toArray());
        $quantity = fake()->numberBetween(1, 100);

        // For Venta and Ajuste_Negativo, make quantity negative
        if (in_array($movementType, MovementType::getDecrementMovementTypes())) {
            $quantity *= -1;
        }

        return [
            'id_product' => Product::factory(),
            'id_order' => $movementType === 'Compra' || $movementType === 'Venta' 
                ? Order::factory() 
                : 1,
            'id_user_responsible' => User::factory(),
            'id_movement_type' => MovementType::where('name', $movementType)->first()->id,
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
                'id_movement_type' => MovementType::where('name', 'Compra')->first()->id,
                'quantity_moved' => fake()->numberBetween(1, 100),
                'external_reference' => fake()->bothify('PO##-####')
            ];
        });
    }

    public function sale(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'id_movement_type' => MovementType::where('name', 'Venta')->first()->id,
                'quantity_moved' => fake()->numberBetween(-100, -1),
                'external_reference' => fake()->bothify('SO##-####')
            ];
        });
    }

    public function adjustment(bool $positive = true): Factory
    {
        return $this->state(function (array $attributes) use ($positive) {
            $quantity = fake()->numberBetween(1, 50);
            $movementType = $positive ? 'Ajuste_Positivo' : 'Ajuste_Negativo';
            return [
                'id_movement_type' => MovementType::where('name', $movementType)->first()->id,
                'quantity_moved' => $positive ? $quantity : -$quantity,
                'external_reference' => fake()->bothify('ADJ##-####')
            ];
        });
    }
}