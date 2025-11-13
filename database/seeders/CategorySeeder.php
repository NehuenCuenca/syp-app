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
            ['name' => 'Aceites Aerosol'],
            ['name' => 'Algodones'],
            ['name' => 'Alcohol y Gel'],
            ['name' => 'Alicates'],
            ['name' => 'Botiquin'],
            ['name' => 'Perfumeria'],
            ['name' => 'Tampones'],
            ['name' => 'Azufres'],
            ['name' => 'Bolsitas'],
            ['name' => 'Bolsas Camiseta'],
            ['name' => 'Cepillos Dentales'],
            ['name' => 'Cigarrillos y Tabacos'],
            ['name' => 'Cinta Embalar'],
            ['name' => 'Bombillas'],
            ['name' => 'Crema Dental'],
            ['name' => 'Encendedores'],
            ['name' => 'Equipo Ciclista'],
            ['name' => 'Escarbadientes'],
            ['name' => 'Filos (maq.Afeitar)'],
            ['name' => 'Coleros y Cordones'],
            ['name' => 'Globos'],
            ['name' => 'Insecticidas y Repelentes'],
            ['name' => 'Jabones Tocador'],
            ['name' => 'Lamparas Led'],
            ['name' => 'Hilos'],
            ['name' => 'Librería'],
            ['name' => 'Gases y Bencinas'],
            ['name' => 'Analgésicos'],
            ['name' => 'Naipes'],
            ['name' => 'Pañuelitos'],
            ['name' => 'Papelillos (hojillas)'],
            ['name' => 'Filtros'],
            ['name' => 'Pegamentos'],
            ['name' => 'Mamaderas y Tetinas'],
            ['name' => 'Pomadas'],
            ['name' => 'Peines'],
            ['name' => 'Pañales'],
            ['name' => 'Pilas'],
            ['name' => 'Preservativos'],
            ['name' => 'Shampoos'],
            ['name' => 'Talcos'],
            ['name' => 'Termos'],
            ['name' => 'Pilas especiales'],
            ['name' => 'Memorias y pendrive'],
            ['name' => 'Toallitas y Protectores'],
            ['name' => 'Velas'],
            ['name' => 'Varios'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Generar categorías de prueba con factory
        // Category::factory()->count(3)->create();
    }
}
