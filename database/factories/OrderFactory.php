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
        $totalGross = fake()->randomFloat(2, 100, 10000);
        $taxRate = 0.21; // 21% tax rate
        $totalTaxes = $totalGross * $taxRate;
        $totalNet = $totalGross + $totalTaxes;

        $estimatedDeliveryDate = fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d');
        $shouldHaveActualDelivery = fake()->boolean(70); // 70% chance of having actual delivery date

        return [
            'id_contact' => Contact::factory(),
            'id_user_creator' => User::factory(),
            'estimated_delivery_date' => $estimatedDeliveryDate,
            'actual_delivery_date' => $shouldHaveActualDelivery ? 
                fake()->dateTimeBetween($estimatedDeliveryDate, '+35 days')->format('Y-m-d') : 
                null,
            'order_type' => fake()->randomElement(['Compra_Entrante', 'Venta_Saliente']),
            'order_status' => fake()->randomElement(['Pendiente', 'Completado', 'Cancelado', 'Devuelto']),
            'total_gross' => $totalGross,
            'total_taxes' => $totalTaxes,
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
            return ['order_type' => 'Purchase_In'];
        });
    }

    /**
     * Configure the factory to create a sale order.
     */
    public function sale(): static
    {
        return $this->state(function (array $attributes) {
            return ['order_type' => 'Sale_Out'];
        });
    }
}