<?php

namespace Database\Factories;

use App\Models\MovementType;
use App\Models\Order;
use App\Models\OrderDetail;
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
            'product_id' => Product::factory(),
            'id_order' => $movementType === 'Compra' || $movementType === 'Venta' 
                ? Order::factory() 
                : 1,
            'order_detail_id' => OrderDetail::factory() || 1,
            'movement_type_id' => MovementType::where('name', $movementType)->first()->id,
            'quantity_moved' => $quantity,
            'notes' => fake()->boolean(70) 
                ? fake()->sentence() 
                : null,
        ];
    }

    public function purchase(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'movement_type_id' => MovementType::where('name', 'Compra')->first()->id,
                'quantity_moved' => fake()->numberBetween(1, 100),
                'external_reference' => fake()->bothify('PO##-####')
            ];
        });
    }

    public function sale(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'movement_type_id' => MovementType::where('name', 'Venta')->first()->id,
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
                'movement_type_id' => MovementType::where('name', $movementType)->first()->id,
                'quantity_moved' => $positive ? $quantity : -$quantity,
                'external_reference' => fake()->bothify('ADJ##-####')
            ];
        });
    }
}