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
        $totalNet = fake()->randomFloat(2, 100, 10000);
        $orderType = fake()->randomElement(['Compra', 'Venta']);
        $todayTimestamp = now()->timestamp;

        return [
            'id_contact' => Contact::factory(),
            'code' => $todayTimestamp,
            'id_movement_type' => MovementType::firstWhere('name', $orderType)->id,
            'total_net' => $totalNet,
            'notes' => fake()->optional(0.7)->text(200), // 70% chance of having notes
        ];
    }

    /**
     * Configure the factory to create a purchase order.
     */
    public function purchase(): static
    {
        return $this->state(function (array $attributes) {
            return ['id_movement_type' => MovementType::firstWhere('name', 'Compra')->id];
        });
    }

    /**
     * Configure the factory to create a sale order.
     */
    public function sale(): static
    {
        return $this->state(function (array $attributes) {
            return ['id_movement_type' =>  MovementType::firstWhere('name', 'Venta')->id];
        });
    }
}