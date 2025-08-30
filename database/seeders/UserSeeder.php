<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin user
        User::create([
            'username' => 'Sofía Distribuciones',
            'email' => 'sergioross73@hotmail.com',
            'password' => Hash::make('Admin123'),
            'role' => 'Admin'
        ]);

        // Create a typical user
        User::create([
            'username' => 'john.doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('user123'),
            'role' => 'Usuario'
        ]);
    }
}