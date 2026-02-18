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
            'phone' => '3446001234',
            'password' => Hash::make('admin123'),
            'role' => User::ROLE_ADMIN
        ]);

        // Create a typical user
        User::create([
            'username' => 'john.doe',
            'email' => 'john.doe@example.com',
            'phone' => '3446112233',
            'password' => Hash::make('user123'),
            'role' => User::ROLE_USER
        ]);
    }
}