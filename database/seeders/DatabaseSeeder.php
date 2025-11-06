<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ContactSeeder::class,
            ProductSeeder::class,
            MovementTypeSeeder::class,
            OrderSeeder::class,
            OrderDetailSeeder::class,
            StockMovementSeeder::class,
        ]);
    }
}
