<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Create a typical user
        User::create([
            'username' => 'john.doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('user123'),
            'role' => 'user'
        ]);
    }
}