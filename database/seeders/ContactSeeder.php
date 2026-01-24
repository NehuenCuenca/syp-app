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

        // Create a typical contact
        Contact::create([
            'name' => 'Acme Corporation',
            'email' => 'john.doe@acme.com',
            'phone' => '(555) 123-4567',
            'address' => '123 Business Ave, Suite 100, Enterprise City, 12345',
            'contact_type' => Contact::CONTACT_TYPE_CLIENT,
        ]);

        // Create 15 random contacts
        Contact::factory(15)->create();
    }
}