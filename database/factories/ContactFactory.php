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
        $typePrefix = ($type === 'Cliente') ? 'CLI' : 'PROV';
        $companyName = $this->faker->company();
        $code = $typePrefix . '-' . strtoupper(substr(preg_replace('/\s+/', '', $companyName), 0, 4)) . '-' . $this->faker->unique()->numberBetween(100, 999);

        return [
            'code' => $code,
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