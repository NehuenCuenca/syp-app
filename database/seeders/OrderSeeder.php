<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing user and contact
        $user = User::where('email', 'sergioross73@hotmail.com')->first() ?? User::factory()->create();
        $contact = Contact::where('email', 'john.doe@acme.com')->first() ?? Contact::factory()->create();

        // Create a typical sale order
        Order::create([
            'id_contact' => $contact->id,
            'id_user_creator' => $user->id,
            'actual_delivery_date' => null,
            'code' => strtoupper(substr('Venta', 0, 3)) . '-' . now()->timestamp,
            'order_type' => 'Venta',
            'order_status' => 'Pendiente',
            'total_net' => 1512.50,
            'notes' => 'Standard office equipment order for Acme Corporation'
        ]);

        // Create 5 random orders
        Order::factory(5)->create([
            'id_user_creator' => $user->id // All orders created by Admin
        ]);
    }
}