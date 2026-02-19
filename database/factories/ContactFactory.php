<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(Contact::getContactTypes());
        $companyName = $this->faker->company();

        return [
            'name' => $companyName,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'contact_type' => $type,
        ];
    }
}