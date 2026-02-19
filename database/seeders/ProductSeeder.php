<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $realProducts = [
            [
                'name' => '15W40 x 150 cc.',
                'category_id' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => '15W40 x 331 grs.',
                'category_id' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Nechi x 250 cc.',
                'category_id' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Nechi x 400cc.',
                'category_id' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Aceite maquina liquido',
                'category_id' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Económico x 75 grs.',
                'category_id' => 2, // 'Algodones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Estrella x 75grs.',
                'category_id' => 2, // 'Algodones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cotonetes pote x 120u.',
                'category_id' => 2, // 'Algodones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alcohol Acktiol aer. X 125cc.',
                'category_id' => 3, // 'Alcohol y Gel',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alcohol Rexona aer.',
                'category_id' => 3, // 'Alcohol y Gel',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alcohol gel cartera',
                'category_id' => 3, // 'Alcohol y Gel',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diasmar grande',
                'category_id' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diasmar chico',
                'category_id' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lima uñas Nº11 madera x 12u.',
                'category_id' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pinza Depilar x 12u.',
                'category_id' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Invisible XZN negra',
                'category_id' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Agua Oxigenada',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bicarbonato sobres',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Curitas',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gasas',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Manteca de cacao',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Solucion fisiologica',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tela adhesiva medicinal 9 mts',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tela medicinal 2 mts',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Termometro digital',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Barbijo x 50u.',
                'category_id' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Dove Roll Onn',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Dove Aero fem',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Dove Aero men',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Axe Aer.',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Rexona Fem Aer',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Rexona Aer. Hombre',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Rexona Roll Onn',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Rexona Barra',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Impulse aero.',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Odorono crema',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Odorono Aero.',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Patrichs',
                'category_id' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tampones OB x 8u.',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tampones Nosotras x 12 u',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Crema Nivea lata 60 grs',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Quitaesmalte Doree',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Quitaesmalte Cutex',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gel Lord Cheseline',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gel Doree pomo x 250cc.',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Espuma afeitar Gillette',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hinds 125 cc',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hinds 250 cc',
                'category_id' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'R J x 5u.',
                'category_id' => 8, // 'Azufres',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'R J separador x 50u.',
                'category_id' => 8, // 'Azufres',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Japonesas bolsa x 100u.',
                'category_id' => 9, // 'Bolsitas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 20 x 30',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 30 x 40',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 40 x 50',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 40x50 eco. reforz.',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 40x50 eco. blanca',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 50 x 70',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 45 x 60',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 45x55 ciudad verde',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Consorcio 90x120',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Consorcio 60x90',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Consorcio 80x110',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Residuos 45x60',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Residuos 50x70',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol.caramelo Rendidor 15x20',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol.caramelo Rendidor 15x25',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 20x30',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 25x35',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 30x40',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 35x45',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 40x50',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bolsa papel kraft N ° 3',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bolsa papel kraft N ° 4 A',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bolsa papel kraft N ° 6 X 100 un',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bolsa papel kraft N ° 7 X 100 un',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rollo adherente (film) x 15mts.',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Papel aluminio',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lamina separador fiambres20x25',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Vasos 220 cc x 100 unid.',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Vasos 1lt',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Vasos 500 cc',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cucharitas x 50 unid.',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Guantes polietileno x 100u. ',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sorbetes x 1000',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Celofan bolsa 10x15',
                'category_id' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'ALMA CURVA x 12u.',
                'category_id' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bombilla Hexagonal largax 12u',
                'category_id' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro p/ bombilla 2 unid',
                'category_id' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Colgate',
                'category_id' => 11, // 'Cepillos Dentales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Macao x 12u.',
                'category_id' => 11, // 'Cepillos Dentales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Colgate x 90grs.',
                'category_id' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kolynos x 70grs. Original',
                'category_id' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Odol x 70grs.',
                'category_id' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Boxer KS',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dolchester',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pier',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Golden Mentol Box',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Golden Soft KS',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kiel',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Master KS',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Milenio Convertible',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mill Convertible',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mill',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Melbourne',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Melbourne Soft',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Red Point On Convertible',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Red Point Común',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Red Point Mentol',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Van Kiff',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Golden',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Cuatro Leguas',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Las Hojas',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Las Hojas Premium',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Van Hassen natural',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco P/pipa Argento',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Don Jose Negro',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Cerrito',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Red Field',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Sayri 30 grs',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco pachamama',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Achalay',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Picador plástico Stamps grande',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Picador metalico Tambor revolver',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Picador metalico Tuerca Chica',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Picador metalico Hexagonal',
                'category_id' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Aisladora 10 Mts',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Embalar transparente x 100 mts',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Embalar transparente x 40 mts',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta papel 12 mm',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta papel 18 mm',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta papel 24 mm',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta Confitera',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta Scotch 30 mts',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rollo etiqueta',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Etiqueta p/balanza',
                'category_id' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'ALMA CURVA x 12u.',
                'category_id' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bombilla Hxagonal largax 12u',
                'category_id' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro p/ bombilla 2 unid',
                'category_id' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Colgate x 90grs.',
                'category_id' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Odol x 70grs.',
                'category_id' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kolynos x 70grs. Original ',
                'category_id' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bic Mini x 6',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bic Maxi x 6 ',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'BX7 Opaco x 50u.',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc.  Econom. X 25',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc. Clipper x 5 unid',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Candela Transparente x 25u.',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Magiclick Transparente x 25 u.',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc.Elec, Magiclick Pop x 15u',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Encendedor cocina largo',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Encendedor cocina CORTO',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc.electronico Candela x 20',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Fosforos Tres Patitos',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc. Catalitico Magiclick x 15',
                'category_id' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Parche Num.2 x 72',
                'category_id' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Parche NUM.3 x 72 ',
                'category_id' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Parche NUM.4 x 60',
                'category_id' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Solucion p/parches x 10u.',
                'category_id' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gomines x 50 unid.',
                'category_id' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Equipo ciclista indiv.',
                'category_id' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lince x 12u.',
                'category_id' => 18, // 'Escarbadientes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Sensitive',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Twin',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Confort 3f.',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Confort 2f.',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Soleil',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prestobarba Ultragrip 2f.',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prestobarba  Mujer ( 8u )',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Maquina Minora',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Maquina Mach 3',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repuesto Mach 3 ( x 2u.)',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Maquina Astra gillette ( 12 unid)',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hoja afeitar Gillette roja x 5u.',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prestobarba 3f. (X 5u.)',
                'category_id' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Coleros Tubo surt.x 14u.',
                'category_id' => 20, // 'Coleros y Cordones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cordones x 110',
                'category_id' => 20, // 'Coleros y Cordones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Coleros . X 12u.',
                'category_id' => 20, // 'Coleros y Cordones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Espiral Raid 12 unidades',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tableta Fuyi Vape x 24u.',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tableta Raid x 24u.',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Raid aparato vape',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Fuyi MMM aero',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Insecticida Raid azul aerosol',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lysoform Aero',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Raid cucarachicida negro',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repelente Off aerosol',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repelente Off crema',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repelente Econo Aero',
                'category_id' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rexona x 125grs',
                'category_id' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lux x 125 grs',
                'category_id' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Q-Sens x 3 unid.',
                'category_id' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dove antibacterial',
                'category_id' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dove 90 grs.',
                'category_id' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 7wts.',
                'category_id' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 9wts.',
                'category_id' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 10wts.',
                'category_id' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 12wts.',
                'category_id' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 15wts.',
                'category_id' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hilo algodon ovillo grande x 10',
                'category_id' => 25, // 'Hilos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hilo coser blanc /negro/surt. x 12 u',
                'category_id' => 25, // 'Hilos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Canastita aguja',
                'category_id' => 25, // 'Hilos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lápiz negro BIC x 12u.',
                'category_id' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lapicera BIC trazo grueso',
                'category_id' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lapiz corrector Filgo',
                'category_id' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Plasticola x 100 grs.',
                'category_id' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Voligoma',
                'category_id' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lapicera BIC azul t/f ',
                'category_id' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calculadora 12 digitos',
                'category_id' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gas Clear chico x 160 cc.',
                'category_id' => 27, // 'Gases y Bencinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gas Clear Gde. X 440 cc.',
                'category_id' => 27, // 'Gases y Bencinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bencina Claer x 150cc.',
                'category_id' => 27, // 'Gases y Bencinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Piedra p/ encend. X 20 u.Cerium',
                'category_id' => 27, // 'Gases y Bencinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'La Española  x 50',
                'category_id' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Economico x 50',
                'category_id' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Habana x 50',
                'category_id' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Casino x 50',
                'category_id' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Casino Pocker x 54',
                'category_id' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'La Española x 40',
                'category_id' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Habana x 40',
                'category_id' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Casino x 40',
                'category_id' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Carilina Ellite x 6u.',
                'category_id' => 30, // 'Pañuelitos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB Blanca x 25',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB Gris x 25',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB Negra x 25',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB Organica x 25',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Weedy / ZEUS celulósica (unidad)',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Stamps Organica /negrax25',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Stamps celulósica',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pier organica/negra x 25',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Smoking  50 x 25',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Smoking 75 x 25',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hojilla Zeus negra x2',
                'category_id' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Regular x 100u.',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Slim x 120u.',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Slim extra',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Mentol x 150',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Long Slim',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Organico',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB tips negro x 25',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro Libella slim',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro Libella  Mentol',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro Libella Regular',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro DRpin regular',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro DRpin slim',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Microboquilla David Ross',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Boquilla Minifusor',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repuesto Minifusor',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tips Stamps Org. ',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB maquina de armar METAL',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Zeus maquina de armar PLASTICA',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hojilla Gizzeh',
                'category_id' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Fastix',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Fastix alta temperatura ',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'La Gotita',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'La Gotita gel',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pulpito',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Poxilina',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Poxipol',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Poxiran',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Unipox',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Uhu',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ecole',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gotita Pegamil',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gotita Suprabond',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Unipox extra fuerte',
                'category_id' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mamaderas',
                'category_id' => 34, // 'Mamaderas y Tetinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Chupetes anatómicos x 6u.',
                'category_id' => 34, // 'Mamaderas y Tetinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tetinas siliconas x 10u.',
                'category_id' => 34, // 'Mamaderas y Tetinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Washintong marron/negra',
                'category_id' => 35, // 'Pomadas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Peine de bolsillo x 12u',
                'category_id' => 36, // 'Peines',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Peine Familiar x 12u.',
                'category_id' => 36, // 'Peines',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Babysec',
                'category_id' => 37, // 'Pañales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Huggies',
                'category_id' => 37, // 'Pañales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell AA',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell AAA',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell Bateria 9vt.',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell Mediana C',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell Grande D',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer AA tira 10 unid',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer AAA tira 10 unid',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer Mediana C',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer Grande D',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer Bateria 9vt.',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready AA',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready AAA',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Mediana C',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Charola D',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Grande D',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Bateria 9vt.',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Tubo paq.AA ',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Tubo AAA',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac recarg. AA x 2u.',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac recarg. AAA x 2u.',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizzer AA blíster 4 unid.',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizzer AAA blíster 4 unid.',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac AA tubo 4 unid.( 48)',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac AAA tubo 4 unid.( 48)',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac AAA tubo 2 unid .(30)',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cargador pilas AA/AAA',
                'category_id' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime super fino',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Texturado',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Lubricado',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Ultra fino',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Espermicida',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Tachas',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Mega',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Warring',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Retardante',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Anatómico',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Zero',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Sking',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Tulipan',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Tulipan Excibidor surt.x 26',
                'category_id' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sedal Shampoo sobres x 24u.',
                'category_id' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sedal Crema enjuague sobres x 24u.',
                'category_id' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sedal Shampoo/ enj x 200cc.',
                'category_id' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pantene Shampoo/ enj 200cc.',
                'category_id' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pantene Shampoo sobres x 24u.',
                'category_id' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pantene Crema enj. Sobres x 24u.',
                'category_id' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Shampo Dove x 200cc.',
                'category_id' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Crema enjuague Dove x 200cc.',
                'category_id' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dove shampoo/enjuague sachet x 24',
                'category_id' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Efficient x 100grs.',
                'category_id' => 41, // 'Talcos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Belabin x 100grs.p/cuerpo',
                'category_id' => 41, // 'Talcos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Efficient Aero',
                'category_id' => 41, // 'Talcos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lumilagro  Acero Inox ',
                'category_id' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lumilagro x 1lt.',
                'category_id' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tapon termo común',
                'category_id' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tapon Saturno con tapita',
                'category_id' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mate listo Taragui',
                'category_id' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Termo listo Taragui',
                'category_id' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 1220',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 1216',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2016',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 1620',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2025',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2032',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2450',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2012',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. CR 123',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. A76 X 10',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. A23 (unidad)',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. A27 (unidad)',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pilas LR 1130/LR54/AG10 X 10',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pilas LR 44 x 5unid.',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Renatta 377 x 10u.',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'LR41 x5 unid.',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila Audifono AC13 x6 Rayovac',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pilas 1632 Renata',
                'category_id' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Memoria 32 Gb',
                'category_id' => 44, // 'Memorias y pendrive',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Memoria 64 Gb',
                'category_id' => 44, // 'Memorias y pendrive',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pendrive 32 Gb',
                'category_id' => 44, // 'Memorias y pendrive',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pendrive 16 Gb',
                'category_id' => 44, // 'Memorias y pendrive',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pendrive 16 Gb',
                'category_id' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 20u 45x60',
                'category_id' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 20u 50X70',
                'category_id' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 10u 60x90',
                'category_id' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 10u 80x110',
                'category_id' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 10u 80x110',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella nocturna',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calipso toa. Común',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calipso toa. Nocturna',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calipso protector',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calipso protector cola less',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella toa c/alas x8 ',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella toa ultra fina',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella protector',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella tanga',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lady soft toa. verde',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lady Soft u/delgada',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lina toa, c/alas',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lina toa, nocturna',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lina protect.diario',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lina Toa.incontinencia',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kotex toa.normal',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kotex toa. Ultra fina antibacterial ',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kotex toa. nocturna',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kotex protect. Diario',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Nosotras toa normal',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Toallita Humedas Belabin x 100',
                'category_id' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Jacaranda parafina',
                'category_id' => 46, // 'Velas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bengalitas x 4u.',
                'category_id' => 46, // 'Velas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Velon Cumpleaños x 20',
                'category_id' => 46, // 'Velas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tanza Bordeadora fina',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tanza Bordeadora gruesa',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Teflon angosto',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calorito reforzado p/termos',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Caloritos metalicos',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Escarapelas x 24',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Broches plásticos x 12 ',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Anilinas azul/negra',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cuchillos descartables x 50 ',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tenedores descartables x 50',
                'category_id' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Actron 400 x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Actron Mujer x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Actron Plus',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alergia x 10 (loratidina)',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alikal sobres',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Amoxidal x 8',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bucoangin caramelos x 9 ',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Amoxicilina',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Amoxicilina 875 x 7',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Amoxicilina 875 + acido clavulani',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Anaflex x 8',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Anaflex Plus',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Aspirinetas x 98 ',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => ' Bayaspirina x 30u',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bayaspirina C fria sobres',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bayaspirina C caliente sobres',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Buscapina x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Buscapina Perlas x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cafiaspirina x 30',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Carbon x 10 past.',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cefalexina 500 x 8',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Chicle Laxante',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diclofenac 75 mg.',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diclofenac Pridinol x 15',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diclofenac gel pomo',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dorixina x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Famotidina 20 mg (antiácido)x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibu Evanol r/accion x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibu Evanol Plus x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibu Evanol Plus x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibupirac x 12',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibuprofeno 400 x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibuprofeno 600x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Keterolac sublingual x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Keterolac x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Loperamida x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Migral x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mejoral x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Next comprimidos',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Novalgina x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Qura Plus x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Paracetamol',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Paracetamol 1 gr x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rennie x 12',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sertal x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sindenafil 100 mg x 1 ',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Vita antigripal',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sertal Perlas x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabcin x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Forte x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Plus x 8',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Omeprazol x 15',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Duo x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Resaca',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Espasmo',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Vent 3',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Vick sobres',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Vick Forte sobres',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Next',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Uvasal sobres',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Viagra x 2',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Viagra masticable x 2',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol 1 mgr. X 8',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Migra x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Refrianex x 10',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Vick lata 12 grs',
                'category_id' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
        ];

        foreach ($realProducts as $product) {
            Product::create($product);
        }
    }
}