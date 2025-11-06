<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\MovementType;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing user and contact
        $contact = Contact::where('email', 'john.doe@acme.com')->first() ?? Contact::factory()->create();

        // Create a typical sale order
        Order::create([
            'id_contact' => $contact->id,
            'code' => now()->timestamp,
            'id_movement_type' => MovementType::firstWhere('name', 'Venta')->id,
            'total_net' => 1512.50,
            'notes' => 'Standard office equipment order for Acme Corporation'
        ]);

        // Create 5 random orders
        Order::factory(5)->create();
    }
}