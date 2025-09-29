<?php

namespace Database\Factories;

use App\Models\Contact;
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
        $estimatedDeliveryDate = fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d');
        $shouldHaveActualDelivery = fake()->boolean(70); // 70% chance of having actual delivery date

        return [
            'id_contact' => Contact::factory(),
            'id_user_creator' => User::factory(),
            'code' => strtoupper(substr($orderType, 0, 3)) . '-' . $todayTimestamp,
            'actual_delivery_date' => $shouldHaveActualDelivery ? 
                fake()->dateTimeBetween($estimatedDeliveryDate, '+35 days')->format('Y-m-d') : 
                null,
            'order_type' => $orderType,
            'order_status' => fake()->randomElement(['Pendiente', 'Completado', 'Cancelado']),
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
            return ['order_type' => 'Compra'];
        });
    }

    /**
     * Configure the factory to create a sale order.
     */
    public function sale(): static
    {
        return $this->state(function (array $attributes) {
            return ['order_type' => 'Venta'];
        });
    }
}