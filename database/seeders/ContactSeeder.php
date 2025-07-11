<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        // Create a typical contact
        Contact::create([
            'company_name' => 'Acme Corporation',
            'contact_name' => 'John Doe',
            'email' => 'john.doe@acme.com',
            'phone' => '(555) 123-4567',
            'address' => '123 Business Ave, Suite 100, Enterprise City, 12345',
            'contact_type' => 'Cliente',
            'registered_at' => now(),
        ]);

        // Create 15 random contacts
        Contact::factory(15)->create();
    }
}