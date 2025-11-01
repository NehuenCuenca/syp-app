<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['Cliente', 'Proveedor']);
        $companyName = $this->faker->company();

        return [
            'company_name' => $companyName,
            'contact_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'contact_type' => $type,
            'registered_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}