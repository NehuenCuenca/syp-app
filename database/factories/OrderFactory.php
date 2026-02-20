<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\MovementType;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $adjustmentAmount = fake()->numberBetween(-1000, 1000);
        $subtotal = fake()->numberBetween(100, 1000);
        $totalNet =  $subtotal + $adjustmentAmount;
        $orderType = fake()->randomElement([MovementType::MOVEMENT_TYPE_BUY, MovementType::MOVEMENT_TYPE_SALE]);

        return [
            'contact_id' => Contact::factory(),
            'movement_type_id' => MovementType::firstWhere('name', $orderType)->id,
            'adjustment_amount' => $adjustmentAmount,
            'subtotal' => $subtotal,
            'total_net' => $totalNet,
            'notes' => fake()->text(40),
        ];
    }

    /**
     * Configure the factory to create a purchase order.
     */
    public function purchase(): static
    {
        return $this->state(function (array $attributes) {
            return ['movement_type_id' => MovementType::firstWhere('name', 'Compra')->id];
        });
    }

    /**
     * Configure the factory to create a sale order.
     */
    public function sale(): static
    {
        return $this->state(function (array $attributes) {
            return ['movement_type_id' =>  MovementType::firstWhere('name', 'Venta')->id];
        });
    }
}