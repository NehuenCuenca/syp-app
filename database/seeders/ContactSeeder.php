<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        Contact::create([
            'name' => 'OCASIONAL',
            'email' => null,
            'phone' => null,
            'address' => null,
            'contact_type' => Contact::CONTACT_TYPE_CLIENT,
        ]);

        Contact::factory(20)->create();
    }
}