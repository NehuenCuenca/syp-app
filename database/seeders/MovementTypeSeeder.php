<?php

namespace Database\Seeders;

use App\Models\MovementType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MovementType::create([
            'name' => 'Compra',
            'increase_stock' => true,
        ]);
        MovementType::create([
            'name' => 'Venta',
            'increase_stock' => false,
        ]);
        MovementType::create([
            'name' => 'Ajuste Positivo',
            'increase_stock' => true,
        ]);
        MovementType::create([
            'name' => 'Ajuste Negativo',
            'increase_stock' => false,
        ]);
    }
}
