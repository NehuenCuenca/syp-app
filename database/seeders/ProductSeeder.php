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
                'id_category' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => '15W40 x 331 grs.',
                'id_category' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Nechi x 250 cc.',
                'id_category' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Nechi x 400cc.',
                'id_category' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Aceite maquina liquido',
                'id_category' => 1, // 'Aceites Aerosol',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Económico x 75 grs.',
                'id_category' => 2, // 'Algodones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Estrella x 75grs.',
                'id_category' => 2, // 'Algodones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cotonetes pote x 120u.',
                'id_category' => 2, // 'Algodones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alcohol Acktiol aer. X 125cc.',
                'id_category' => 3, // 'Alcohol y Gel',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alcohol Rexona aer.',
                'id_category' => 3, // 'Alcohol y Gel',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alcohol gel cartera',
                'id_category' => 3, // 'Alcohol y Gel',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diasmar grande',
                'id_category' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diasmar chico',
                'id_category' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lima uñas Nº11 madera x 12u.',
                'id_category' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pinza Depilar x 12u.',
                'id_category' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Invisible XZN negra',
                'id_category' => 4, // 'Alicates',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Agua Oxigenada',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bicarbonato sobres',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Curitas',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gasas',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Manteca de cacao',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Solucion fisiologica',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tela adhesiva medicinal 9 mts',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tela medicinal 2 mts',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Termometro digital',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Barbijo x 50u.',
                'id_category' => 5, // 'Botiquin',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Dove Roll Onn',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Dove Aero fem',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Dove Aero men',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Axe Aer.',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Rexona Fem Aer',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Rexona Aer. Hombre',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Rexona Roll Onn',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Rexona Barra',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Impulse aero.',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Odorono crema',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Odorono Aero.',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Desodorante Patrichs',
                'id_category' => 6, // 'Perfumeria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tampones OB x 8u.',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tampones Nosotras x 12 u',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Crema Nivea lata 60 grs',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Quitaesmalte Doree',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Quitaesmalte Cutex',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gel Lord Cheseline',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gel Doree pomo x 250cc.',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Espuma afeitar Gillette',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hinds 125 cc',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hinds 250 cc',
                'id_category' => 7, // 'tampones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'R J x 5u.',
                'id_category' => 8, // 'Azufres',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'R J separador x 50u.',
                'id_category' => 8, // 'Azufres',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Japonesas bolsa x 100u.',
                'id_category' => 9, // 'Bolsitas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 20 x 30',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 30 x 40',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 40 x 50',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 40x50 eco. reforz.',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 40x50 eco. blanca',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 50 x 70',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 45 x 60',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Camiseta 45x55 ciudad verde',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Consorcio 90x120',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Consorcio 60x90',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Consorcio 80x110',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Residuos 45x60',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Residuos 50x70',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol.caramelo Rendidor 15x20',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol.caramelo Rendidor 15x25',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 20x30',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 25x35',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 30x40',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 35x45',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bol. arranque Rendidor 40x50',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bolsa papel kraft N ° 3',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bolsa papel kraft N ° 4 A',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bolsa papel kraft N ° 6 X 100 un',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bolsa papel kraft N ° 7 X 100 un',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rollo adherente (film) x 15mts.',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Papel aluminio',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lamina separador fiambres20x25',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Vasos 220 cc x 100 unid.',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Vasos 1lt',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Vasos 500 cc',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cucharitas x 50 unid.',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Guantes polietileno x 100u. ',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sorbetes x 1000',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Celofan bolsa 10x15',
                'id_category' => 10, // 'Bolsas Camiseta',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'ALMA CURVA x 12u.',
                'id_category' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bombilla Hexagonal largax 12u',
                'id_category' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro p/ bombilla 2 unid',
                'id_category' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Colgate',
                'id_category' => 11, // 'Cepillos Dentales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Macao x 12u.',
                'id_category' => 11, // 'Cepillos Dentales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Colgate x 90grs.',
                'id_category' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kolynos x 70grs. Original',
                'id_category' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Odol x 70grs.',
                'id_category' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Boxer KS',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dolchester',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pier',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Golden Mentol Box',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Golden Soft KS',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kiel',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Master KS',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Milenio Convertible',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mill Convertible',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mill',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Melbourne',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Melbourne Soft',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Red Point On Convertible',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Red Point Común',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Red Point Mentol',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Van Kiff',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Golden',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Cuatro Leguas',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Las Hojas',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Las Hojas Premium',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Van Hassen natural',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco P/pipa Argento',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Don Jose Negro',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Cerrito',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Red Field',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Sayri 30 grs',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco pachamama',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabaco Achalay',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Picador plástico Stamps grande',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Picador metalico Tambor revolver',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Picador metalico Tuerca Chica',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Picador metalico Hexagonal',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Aisladora 10 Mts',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Embalar transparente x 100 mts',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Embalar transparente x 40 mts',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta papel 12 mm',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta papel 18 mm',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta papel 24 mm',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta Confitera',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cinta Scotch 30 mts',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rollo etiqueta',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Etiqueta p/balanza',
                'id_category' => 13, // 'Cinta Embalar',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'ALMA CURVA x 12u.',
                'id_category' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bombilla Hxagonal largax 12u',
                'id_category' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro p/ bombilla 2 unid',
                'id_category' => 14, // 'Bombillas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Colgate x 90grs.',
                'id_category' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Odol x 70grs.',
                'id_category' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kolynos x 70grs. Original ',
                'id_category' => 15, // 'Crema Dental',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bic Mini x 6',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bic Maxi x 6 ',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'BX7 Opaco x 50u.',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc.  Econom. X 25',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc. Clipper x 5 unid',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Candela Transparente x 25u.',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Magiclick Transparente x 25 u.',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc.Elec, Magiclick Pop x 15u',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Encendedor cocina largo',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Encendedor cocina CORTO',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc.electronico Candela x 20',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Fosforos Tres Patitos',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Enc. Catalitico Magiclick x 15',
                'id_category' => 16, // 'Encendedores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Parche Num.2 x 72',
                'id_category' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Parche NUM.3 x 72 ',
                'id_category' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Parche NUM.4 x 60',
                'id_category' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Solucion p/parches x 10u.',
                'id_category' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gomines x 50 unid.',
                'id_category' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Equipo ciclista indiv.',
                'id_category' => 17, // 'Equipo Ciclista',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lince x 12u.',
                'id_category' => 18, // 'Escarbadientes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Sensitive',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Twin',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Confort 3f.',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Confort 2f.',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Afeita Bic Soleil',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prestobarba Ultragrip 2f.',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prestobarba  Mujer ( 8u )',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Maquina Minora',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Maquina Mach 3',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repuesto Mach 3 ( x 2u.)',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Maquina Astra gillette ( 12 unid)',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hoja afeitar Gillette roja x 5u.',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prestobarba 3f. (X 5u.)',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Coleros Tubo surt.x 14u.',
                'id_category' => 20, // 'Coleros y Cordones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cordones x 110',
                'id_category' => 20, // 'Coleros y Cordones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Coleros . X 12u.',
                'id_category' => 20, // 'Coleros y Cordones',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Espiral Raid 12 unidades',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tableta Fuyi Vape x 24u.',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tableta Raid x 24u.',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Raid aparato vape',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Fuyi MMM aero',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Insecticida Raid azul aerosol',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lysoform Aero',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Raid cucarachicida negro',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repelente Off aerosol',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repelente Off crema',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repelente Econo Aero',
                'id_category' => 22, // 'Insecticidas y Repelentes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rexona x 125grs',
                'id_category' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lux x 125 grs',
                'id_category' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Q-Sens x 3 unid.',
                'id_category' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dove antibacterial',
                'id_category' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dove 90 grs.',
                'id_category' => 23, // 'Jabones Tocador',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 7wts.',
                'id_category' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 9wts.',
                'id_category' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 10wts.',
                'id_category' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 12wts.',
                'id_category' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Led 15wts.',
                'id_category' => 24, // 'Lamparas Led',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hilo algodon ovillo grande x 10',
                'id_category' => 25, // 'Hilos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hilo coser blanc /negro/surt. x 12 u',
                'id_category' => 25, // 'Hilos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Canastita aguja',
                'id_category' => 25, // 'Hilos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lápiz negro BIC x 12u.',
                'id_category' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lapicera BIC trazo grueso',
                'id_category' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lapiz corrector Filgo',
                'id_category' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Plasticola x 100 grs.',
                'id_category' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Voligoma',
                'id_category' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lapicera BIC azul t/f ',
                'id_category' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calculadora 12 digitos',
                'id_category' => 26, // 'Libreria',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gas Clear chico x 160 cc.',
                'id_category' => 27, // 'Gases y Bencinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gas Clear Gde. X 440 cc.',
                'id_category' => 27, // 'Gases y Bencinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bencina Claer x 150cc.',
                'id_category' => 27, // 'Gases y Bencinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Piedra p/ encend. X 20 u.Cerium',
                'id_category' => 27, // 'Gases y Bencinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'La Española  x 50',
                'id_category' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Economico x 50',
                'id_category' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Habana x 50',
                'id_category' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Casino x 50',
                'id_category' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Casino Pocker x 54',
                'id_category' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'La Española x 40',
                'id_category' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Habana x 40',
                'id_category' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Casino x 40',
                'id_category' => 29, // 'Naipes',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Carilina Ellite x 6u.',
                'id_category' => 30, // 'Pañuelitos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB Blanca x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB Gris x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB Negra x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB Organica x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Weedy / ZEUS celulósica (unidad)',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Stamps Organica /negrax25',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Stamps celulósica',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pier organica/negra x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Smoking  50 x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Smoking 75 x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hojilla Zeus negra x2',
                'id_category' => 31, // 'Papelillos (hojillas)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Regular x 100u.',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Slim x 120u.',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Slim extra',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Mentol x 150',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Long Slim',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB filtro Organico',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB tips negro x 25',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro Libella slim',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro Libella  Mentol',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro Libella Regular',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro DRpin regular',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Filtro DRpin slim',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Microboquilla David Ross',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Boquilla Minifusor',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Repuesto Minifusor',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tips Stamps Org. ',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'OCB maquina de armar METAL',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Zeus maquina de armar PLASTICA',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Hojilla Gizzeh',
                'id_category' => 32, // 'FILTROS',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Fastix',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Fastix alta temperatura ',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'La Gotita',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'La Gotita gel',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pulpito',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Poxilina',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Poxipol',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Poxiran',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Unipox',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Uhu',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ecole',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gotita Pegamil',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Gotita Suprabond',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Unipox extra fuerte',
                'id_category' => 33, // 'Pegamentos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mamaderas',
                'id_category' => 34, // 'Mamaderas y Tetinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Chupetes anatómicos x 6u.',
                'id_category' => 34, // 'Mamaderas y Tetinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tetinas siliconas x 10u.',
                'id_category' => 34, // 'Mamaderas y Tetinas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Washintong marron/negra',
                'id_category' => 35, // 'Pomadas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Peine de bolsillo x 12u',
                'id_category' => 36, // 'Peines',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Peine Familiar x 12u.',
                'id_category' => 36, // 'Peines',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Babysec',
                'id_category' => 37, // 'Pañales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Huggies',
                'id_category' => 37, // 'Pañales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell AA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell AAA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell Bateria 9vt.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell Mediana C',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Duracell Grande D',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer AA tira 10 unid',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer AAA tira 10 unid',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer Mediana C',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer Grande D',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizer Bateria 9vt.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready AA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready AAA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Mediana C',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Charola D',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Grande D',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Bateria 9vt.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Tubo paq.AA ',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Everready Tubo AAA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac recarg. AA x 2u.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac recarg. AAA x 2u.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizzer AA blíster 4 unid.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Energizzer AAA blíster 4 unid.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac AA tubo 4 unid.( 48)',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac AAA tubo 4 unid.( 48)',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rayovac AAA tubo 2 unid .(30)',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cargador pilas AA/AAA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime super fino',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Texturado',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Lubricado',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Ultra fino',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Espermicida',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Tachas',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Mega',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Warring',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Retardante',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Anatómico',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Zero',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Sking',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Tulipan',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Prime Tulipan Excibidor surt.x 26',
                'id_category' => 39, // 'Preservativos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sedal Shampoo sobres x 24u.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sedal Crema enjuague sobres x 24u.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sedal Shampoo/ enj x 200cc.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pantene Shampoo/ enj 200cc.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pantene Shampoo sobres x 24u.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pantene Crema enj. Sobres x 24u.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Shampo Dove x 200cc.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Crema enjuague Dove x 200cc.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dove shampoo/enjuague sachet x 24',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Efficient x 100grs.',
                'id_category' => 41, // 'Talcos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Belabin x 100grs.p/cuerpo',
                'id_category' => 41, // 'Talcos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Efficient Aero',
                'id_category' => 41, // 'Talcos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lumilagro  Acero Inox ',
                'id_category' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lumilagro x 1lt.',
                'id_category' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tapon termo común',
                'id_category' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tapon Saturno con tapita',
                'id_category' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mate listo Taragui',
                'id_category' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Termo listo Taragui',
                'id_category' => 42, // 'Termos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 1220',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 1216',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2016',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 1620',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2025',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2032',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2450',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. 2012',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. CR 123',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. A76 X 10',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. A23 (unidad)',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila esp. A27 (unidad)',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pilas LR 1130/LR54/AG10 X 10',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pilas LR 44 x 5unid.',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Renatta 377 x 10u.',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'LR41 x5 unid.',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pila Audifono AC13 x6 Rayovac',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pilas 1632 Renata',
                'id_category' => 43, // 'Pilas Especiales',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Memoria 32 Gb',
                'id_category' => 44, // 'Memorias y pendrive',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Memoria 64 Gb',
                'id_category' => 44, // 'Memorias y pendrive',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pendrive 32 Gb',
                'id_category' => 44, // 'Memorias y pendrive',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pendrive 16 Gb',
                'id_category' => 44, // 'Memorias y pendrive',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Pendrive 16 Gb',
                'id_category' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 20u 45x60',
                'id_category' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 20u 50X70',
                'id_category' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 10u 60x90',
                'id_category' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 10u 80x110',
                'id_category' => 10, // '*NO VA*Novedades',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 10u 80x110',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella nocturna',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calipso toa. Común',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calipso toa. Nocturna',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calipso protector',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calipso protector cola less',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella toa c/alas x8 ',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella toa ultra fina',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella protector',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Doncella tanga',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lady soft toa. verde',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lady Soft u/delgada',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lina toa, c/alas',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lina toa, nocturna',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lina protect.diario',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Lina Toa.incontinencia',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kotex toa.normal',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kotex toa. Ultra fina antibacterial ',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kotex toa. nocturna',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Kotex protect. Diario',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Nosotras toa normal',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Toallita Humedas Belabin x 100',
                'id_category' => 45, // 'Toallitas y Protectores',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Jacaranda parafina',
                'id_category' => 46, // 'Velas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bengalitas x 4u.',
                'id_category' => 46, // 'Velas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Velon Cumpleaños x 20',
                'id_category' => 46, // 'Velas',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tanza Bordeadora fina',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tanza Bordeadora gruesa',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Teflon angosto',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Calorito reforzado p/termos',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Caloritos metalicos',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Escarapelas x 24',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Broches plásticos x 12 ',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Anilinas azul/negra',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cuchillos descartables x 50 ',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tenedores descartables x 50',
                'id_category' => 47, // 'Varios',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Actron 400 x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Actron Mujer x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Actron Plus',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alergia x 10 (loratidina)',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Alikal sobres',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Amoxidal x 8',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bucoangin caramelos x 9 ',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Amoxicilina',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Amoxicilina 875 x 7',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Amoxicilina 875 + acido clavulani',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Anaflex x 8',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Anaflex Plus',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Aspirinetas x 98 ',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => ' Bayaspirina x 30u',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bayaspirina C fria sobres',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Bayaspirina C caliente sobres',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Buscapina x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Buscapina Perlas x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cafiaspirina x 30',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Carbon x 10 past.',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Cefalexina 500 x 8',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Chicle Laxante',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diclofenac 75 mg.',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diclofenac Pridinol x 15',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Diclofenac gel pomo',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Dorixina x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Famotidina 20 mg (antiácido)x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibu Evanol r/accion x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibu Evanol Plus x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibu Evanol Plus x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibupirac x 12',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibuprofeno 400 x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Ibuprofeno 600x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Keterolac sublingual x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Keterolac x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Loperamida x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Migral x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Mejoral x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Next comprimidos',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Novalgina x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Qura Plus x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Paracetamol',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Paracetamol 1 gr x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Rennie x 12',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sertal x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sindenafil 100 mg x 1 ',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Vita antigripal',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Sertal Perlas x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tabcin x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Forte x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Plus x 8',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Omeprazol x 15',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Duo x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Resaca',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Espasmo',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Vent 3',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Vick sobres',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Vick Forte sobres',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Te Next',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Uvasal sobres',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Viagra x 2',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Viagra masticable x 2',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol 1 mgr. X 8',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Tafirol Migra x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Refrianex x 10',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
            [
                'name' => 'Vick lata 12 grs',
                'id_category' => 28, // 'Analgésicos',
                'current_stock' => 10_000,
            ],
        ];

        foreach ($realProducts as $product) {
            Product::create($product);
        }
    }
}