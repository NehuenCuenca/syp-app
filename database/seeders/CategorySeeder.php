<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categorías base
        $categories = [
            ['name' => 'Electrónica'],
            ['name' => 'Hogar'],
            ['name' => 'Ropa'],
            ['name' => 'Alimentos'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Generar categorías de prueba con factory
        Category::factory()->count(3)->create();
    }
}
