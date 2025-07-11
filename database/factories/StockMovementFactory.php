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
            'Compra_Entrante',
            'Venta_Saliente',
            'Ajuste_Positivo',
            'Ajuste_Negativo',
            'Devolucion_Cliente',
            'Devolucion_Proveedor'
        ];

        $movementType = fake()->randomElement($movementTypes);
        $quantity = fake()->numberBetween(1, 100);

        // For Sale_Out and Negative_Adjustment, make quantity negative
        if (in_array($movementType, ['Venta_Saliente', 'Ajuste_Negativo'])) {
            $quantity *= -1;
        }

        return [
            'id_product' => Product::factory(),
            'id_order' => $movementType === 'Compra_Entrante' || $movementType === 'Venta_Saliente' 
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
                'movement_type' => 'Compra_Entrante',
                'quantity_moved' => fake()->numberBetween(1, 100),
                'external_reference' => fake()->bothify('PO##-####')
            ];
        });
    }

    public function sale(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'movement_type' => 'Venta_Saliente',
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
                'movement_type' => $positive ? 'Ajuste_Positivo' : 'Ajuste_Negativo',
                'quantity_moved' => $positive ? $quantity : -$quantity,
                'external_reference' => fake()->bothify('ADJ##-####')
            ];
        });
    }
}