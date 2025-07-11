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
        $user = User::where('email', 'Admin@example.com')->first() ?? User::factory()->create();
        $contact = Contact::where('email', 'john.doe@acme.com')->first() ?? Contact::factory()->create();

        // Create a typical sale order
        Order::create([
            'id_contact' => $contact->id,
            'id_user_creator' => $user->id,
            'estimated_delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'actual_delivery_date' => null,
            'order_type' => 'Venta_Saliente',
            'order_status' => 'Pendiente',
            'total_gross' => 1250.00,
            'total_taxes' => 262.50, // 21% VAT
            'total_net' => 1512.50,
            'notes' => 'Standard office equipment order for Acme Corporation'
        ]);

        // Create 5 random orders
        Order::factory(5)->create([
            'id_user_creator' => $user->id // All orders created by Admin
        ]);
    }
}