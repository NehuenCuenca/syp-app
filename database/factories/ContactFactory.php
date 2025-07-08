<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'contact_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'contact_type' => fake()->randomElement(['cliente', 'proveedor', 'empleado', 'otro']),
            'registered_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}