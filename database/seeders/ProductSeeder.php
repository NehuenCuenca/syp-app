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
            ],
            [
                'name' => '15W40 x 331 grs.',
                'id_category' => 1, // 'Aceites Aerosol',
            ],
            [
                'name' => 'Nechi x 250 cc.',
                'id_category' => 1, // 'Aceites Aerosol',
            ],
            [
                'name' => 'Nechi x 400cc.',
                'id_category' => 1, // 'Aceites Aerosol',
            ],
            [
                'name' => 'Aceite maquina liquido',
                'id_category' => 1, // 'Aceites Aerosol',
            ],
            [
                'name' => 'Económico x 75 grs.',
                'id_category' => 2, // 'Algodones',
            ],
            [
                'name' => 'Estrella x 75grs.',
                'id_category' => 2, // 'Algodones',
            ],
            [
                'name' => 'Cotonetes pote x 120u.',
                'id_category' => 2, // 'Algodones',
            ],
            [
                'name' => 'Alcohol Acktiol aer. X 125cc.',
                'id_category' => 3, // 'Alcohol y Gel',
            ],
            [
                'name' => 'Alcohol Rexona aer.',
                'id_category' => 3, // 'Alcohol y Gel',
            ],
            [
                'name' => 'Alcohol gel cartera',
                'id_category' => 3, // 'Alcohol y Gel',
            ],
            [
                'name' => 'Diasmar grande',
                'id_category' => 4, // 'Alicates',
            ],
            [
                'name' => 'Diasmar chico',
                'id_category' => 4, // 'Alicates',
            ],
            [
                'name' => 'Lima uñas Nº11 madera x 12u.',
                'id_category' => 4, // 'Alicates',
            ],
            [
                'name' => 'Pinza Depilar x 12u.',
                'id_category' => 4, // 'Alicates',
            ],
            [
                'name' => 'Invisible XZN negra',
                'id_category' => 4, // 'Alicates',
            ],
            [
                'name' => 'Agua Oxigenada',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Bicarbonato sobres',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Curitas',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Gasas',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Manteca de cacao',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Solucion fisiologica',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Tela adhesiva medicinal 9 mts',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Tela medicinal 2 mts',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Termometro digital',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Barbijo x 50u.',
                'id_category' => 5, // 'Botiquin',
            ],
            [
                'name' => 'Desodorante Dove Roll Onn',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Dove Aero fem',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Dove Aero men',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Axe Aer.',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Rexona Fem Aer',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Rexona Aer. Hombre',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Rexona Roll Onn',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Rexona Barra',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Impulse aero.',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Odorono crema',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Odorono Aero.',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Desodorante Patrichs',
                'id_category' => 6, // 'Perfumeria',
            ],
            [
                'name' => 'Tampones OB x 8u.',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'Tampones Nosotras x 12 u',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'Crema Nivea lata 60 grs',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'Quitaesmalte Doree',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'Quitaesmalte Cutex',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'Gel Lord Cheseline',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'Gel Doree pomo x 250cc.',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'Espuma afeitar Gillette',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'Hinds 125 cc',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'Hinds 250 cc',
                'id_category' => 7, // 'tampones',
            ],
            [
                'name' => 'R J x 5u.',
                'id_category' => 8, // 'Azufres',
            ],
            [
                'name' => 'R J separador x 50u.',
                'id_category' => 8, // 'Azufres',
            ],
            [
                'name' => 'Japonesas bolsa x 100u.',
                'id_category' => 9, // 'Bolsitas',
            ],
            [
                'name' => 'Camiseta 20 x 30',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Camiseta 30 x 40',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Camiseta 40 x 50',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Camiseta 40x50 eco. reforz.',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Camiseta 40x50 eco. blanca',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Camiseta 50 x 70',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Camiseta 45 x 60',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Camiseta 45x55 ciudad verde',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Consorcio 90x120',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Consorcio 60x90',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Consorcio 80x110',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Residuos 45x60',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Residuos 50x70',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bol.caramelo Rendidor 15x20',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bol.caramelo Rendidor 15x25',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bol. arranque Rendidor 20x30',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bol. arranque Rendidor 25x35',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bol. arranque Rendidor 30x40',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bol. arranque Rendidor 35x45',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bol. arranque Rendidor 40x50',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bolsa papel kraft N ° 3',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bolsa papel kraft N ° 4 A',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bolsa papel kraft N ° 6 X 100 un',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Bolsa papel kraft N ° 7 X 100 un',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Rollo adherente (film) x 15mts.',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Papel aluminio',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Lamina separador fiambres20x25',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Vasos 220 cc x 100 unid.',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Vasos 1lt',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Vasos 500 cc',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Cucharitas x 50 unid.',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Guantes polietileno x 100u. ',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Sorbetes x 1000',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'Celofan bolsa 10x15',
                'id_category' => 10, // 'Bolsas Camiseta',
            ],
            [
                'name' => 'ALMA CURVA x 12u.',
                'id_category' => 14, // 'Bombillas',
            ],
            [
                'name' => 'Bombilla Hexagonal largax 12u',
                'id_category' => 14, // 'Bombillas',
            ],
            [
                'name' => 'Filtro p/ bombilla 2 unid',
                'id_category' => 14, // 'Bombillas',
            ],
            [
                'name' => 'Colgate',
                'id_category' => 11, // 'Cepillos Dentales',
            ],
            [
                'name' => 'Macao x 12u.',
                'id_category' => 11, // 'Cepillos Dentales',
            ],
            [
                'name' => 'Colgate x 90grs.',
                'id_category' => 15, // 'Crema Dental',
            ],
            [
                'name' => 'Kolynos x 70grs. Original',
                'id_category' => 15, // 'Crema Dental',
            ],
            [
                'name' => 'Odol x 70grs.',
                'id_category' => 15, // 'Crema Dental',
            ],
            [
                'name' => 'Boxer KS',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Dolchester',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Pier',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Golden Mentol Box',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Golden Soft KS',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Kiel',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Master KS',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Milenio Convertible',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Mill Convertible',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Mill',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Melbourne',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Melbourne Soft',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Red Point On Convertible',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Red Point Común',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Red Point Mentol',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Van Kiff',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Golden',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Cuatro Leguas',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Las Hojas',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Las Hojas Premium',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Van Hassen natural',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco P/pipa Argento',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Don Jose Negro',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Cerrito',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Red Field',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Sayri 30 grs',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco pachamama',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Tabaco Achalay',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Picador plástico Stamps grande',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Picador metalico Tambor revolver',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Picador metalico Tuerca Chica',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Picador metalico Hexagonal',
                'id_category' => 12, // 'Cigarrillos y Tabacos',
            ],
            [
                'name' => 'Aisladora 10 Mts',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'Embalar transparente x 100 mts',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'Embalar transparente x 40 mts',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'Cinta papel 12 mm',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'Cinta papel 18 mm',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'Cinta papel 24 mm',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'Cinta Confitera',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'Cinta Scotch 30 mts',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'Rollo etiqueta',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'Etiqueta p/balanza',
                'id_category' => 13, // 'Cinta Embalar',
            ],
            [
                'name' => 'ALMA CURVA x 12u.',
                'id_category' => 14, // 'Bombillas',
            ],
            [
                'name' => 'Bombilla Hxagonal largax 12u',
                'id_category' => 14, // 'Bombillas',
            ],
            [
                'name' => 'Filtro p/ bombilla 2 unid',
                'id_category' => 14, // 'Bombillas',
            ],
            [
                'name' => 'Colgate x 90grs.',
                'id_category' => 15, // 'Crema Dental',
            ],
            [
                'name' => 'Odol x 70grs.',
                'id_category' => 15, // 'Crema Dental',
            ],
            [
                'name' => 'Kolynos x 70grs. Original ',
                'id_category' => 15, // 'Crema Dental',
            ],
            [
                'name' => 'Bic Mini x 6',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Bic Maxi x 6 ',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'BX7 Opaco x 50u.',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Enc.  Econom. X 25',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Enc. Clipper x 5 unid',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Candela Transparente x 25u.',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Magiclick Transparente x 25 u.',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Enc.Elec, Magiclick Pop x 15u',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Encendedor cocina largo',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Encendedor cocina CORTO',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Enc.electronico Candela x 20',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Fosforos Tres Patitos',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Enc. Catalitico Magiclick x 15',
                'id_category' => 16, // 'Encendedores',
            ],
            [
                'name' => 'Parche Num.2 x 72',
                'id_category' => 17, // 'Equipo Ciclista',
            ],
            [
                'name' => 'Parche NUM.3 x 72 ',
                'id_category' => 17, // 'Equipo Ciclista',
            ],
            [
                'name' => 'Parche NUM.4 x 60',
                'id_category' => 17, // 'Equipo Ciclista',
            ],
            [
                'name' => 'Solucion p/parches x 10u.',
                'id_category' => 17, // 'Equipo Ciclista',
            ],
            [
                'name' => 'Gomines x 50 unid.',
                'id_category' => 17, // 'Equipo Ciclista',
            ],
            [
                'name' => 'Equipo ciclista indiv.',
                'id_category' => 17, // 'Equipo Ciclista',
            ],
            [
                'name' => 'Lince x 12u.',
                'id_category' => 18, // 'Escarbadientes',
            ],
            [
                'name' => 'Afeita Bic Sensitive',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Afeita Bic Twin',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Afeita Bic Confort 3f.',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Afeita Bic Confort 2f.',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Afeita Bic Soleil',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Prestobarba Ultragrip 2f.',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Prestobarba  Mujer ( 8u )',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Maquina Minora',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Maquina Mach 3',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Repuesto Mach 3 ( x 2u.)',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Maquina Astra gillette ( 12 unid)',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Hoja afeitar Gillette roja x 5u.',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Prestobarba 3f. (X 5u.)',
                'id_category' => 19, // 'Filos (maq.Afeitar)',
            ],
            [
                'name' => 'Coleros Tubo surt.x 14u.',
                'id_category' => 20, // 'Coleros y Cordones',
            ],
            [
                'name' => 'Cordones x 110',
                'id_category' => 20, // 'Coleros y Cordones',
            ],
            [
                'name' => 'Coleros . X 12u.',
                'id_category' => 20, // 'Coleros y Cordones',
            ],
            [
                'name' => 'Espiral Raid 12 unidades',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Tableta Fuyi Vape x 24u.',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Tableta Raid x 24u.',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Raid aparato vape',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Fuyi MMM aero',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Insecticida Raid azul aerosol',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Lysoform Aero',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Raid cucarachicida negro',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Repelente Off aerosol',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Repelente Off crema',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Repelente Econo Aero',
                'id_category' => 22, // 'Insecticidas y Repelentes',
            ],
            [
                'name' => 'Rexona x 125grs',
                'id_category' => 23, // 'Jabones Tocador',
            ],
            [
                'name' => 'Lux x 125 grs',
                'id_category' => 23, // 'Jabones Tocador',
            ],
            [
                'name' => 'Q-Sens x 3 unid.',
                'id_category' => 23, // 'Jabones Tocador',
            ],
            [
                'name' => 'Dove antibacterial',
                'id_category' => 23, // 'Jabones Tocador',
            ],
            [
                'name' => 'Dove 90 grs.',
                'id_category' => 23, // 'Jabones Tocador',
            ],
            [
                'name' => 'Led 7wts.',
                'id_category' => 24, // 'Lamparas Led',
            ],
            [
                'name' => 'Led 9wts.',
                'id_category' => 24, // 'Lamparas Led',
            ],
            [
                'name' => 'Led 10wts.',
                'id_category' => 24, // 'Lamparas Led',
            ],
            [
                'name' => 'Led 12wts.',
                'id_category' => 24, // 'Lamparas Led',
            ],
            [
                'name' => 'Led 15wts.',
                'id_category' => 24, // 'Lamparas Led',
            ],
            [
                'name' => 'Hilo algodon ovillo grande x 10',
                'id_category' => 25, // 'Hilos',
            ],
            [
                'name' => 'Hilo coser blanc /negro/surt. x 12 u',
                'id_category' => 25, // 'Hilos',
            ],
            [
                'name' => 'Canastita aguja',
                'id_category' => 25, // 'Hilos',
            ],
            [
                'name' => 'Lápiz negro BIC x 12u.',
                'id_category' => 26, // 'Libreria',
            ],
            [
                'name' => 'Lapicera BIC trazo grueso',
                'id_category' => 26, // 'Libreria',
            ],
            [
                'name' => 'Lapiz corrector Filgo',
                'id_category' => 26, // 'Libreria',
            ],
            [
                'name' => 'Plasticola x 100 grs.',
                'id_category' => 26, // 'Libreria',
            ],
            [
                'name' => 'Voligoma',
                'id_category' => 26, // 'Libreria',
            ],
            [
                'name' => 'Lapicera BIC azul t/f ',
                'id_category' => 26, // 'Libreria',
            ],
            [
                'name' => 'Calculadora 12 digitos',
                'id_category' => 26, // 'Libreria',
            ],
            [
                'name' => 'Gas Clear chico x 160 cc.',
                'id_category' => 27, // 'Gases y Bencinas',
            ],
            [
                'name' => 'Gas Clear Gde. X 440 cc.',
                'id_category' => 27, // 'Gases y Bencinas',
            ],
            [
                'name' => 'Bencina Claer x 150cc.',
                'id_category' => 27, // 'Gases y Bencinas',
            ],
            [
                'name' => 'Piedra p/ encend. X 20 u.Cerium',
                'id_category' => 27, // 'Gases y Bencinas',
            ],
            [
                'name' => 'La Española  x 50',
                'id_category' => 29, // 'Naipes',
            ],
            [
                'name' => 'Economico x 50',
                'id_category' => 29, // 'Naipes',
            ],
            [
                'name' => 'Habana x 50',
                'id_category' => 29, // 'Naipes',
            ],
            [
                'name' => 'Casino x 50',
                'id_category' => 29, // 'Naipes',
            ],
            [
                'name' => 'Casino Pocker x 54',
                'id_category' => 29, // 'Naipes',
            ],
            [
                'name' => 'La Española x 40',
                'id_category' => 29, // 'Naipes',
            ],
            [
                'name' => 'Habana x 40',
                'id_category' => 29, // 'Naipes',
            ],
            [
                'name' => 'Casino x 40',
                'id_category' => 29, // 'Naipes',
            ],
            [
                'name' => 'Carilina Ellite x 6u.',
                'id_category' => 30, // 'Pañuelitos',
            ],
            [
                'name' => 'OCB Blanca x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'OCB Gris x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'OCB Negra x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'OCB Organica x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'Weedy / ZEUS celulósica (unidad)',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'Stamps Organica /negrax25',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'Stamps celulósica',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'Pier organica/negra x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'Smoking  50 x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'Smoking 75 x 25',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'Hojilla Zeus negra x2',
                'id_category' => 31, // 'Papelillos (hojillas)',
            ],
            [
                'name' => 'OCB filtro Regular x 100u.',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'OCB filtro Slim x 120u.',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'OCB filtro Slim extra',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'OCB filtro Mentol x 150',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'OCB filtro Long Slim',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'OCB filtro Organico',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'OCB tips negro x 25',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Filtro Libella slim',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Filtro Libella  Mentol',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Filtro Libella Regular',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Filtro DRpin regular',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Filtro DRpin slim',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Microboquilla David Ross',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Boquilla Minifusor',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Repuesto Minifusor',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Tips Stamps Org. ',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'OCB maquina de armar METAL',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Zeus maquina de armar PLASTICA',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Hojilla Gizzeh',
                'id_category' => 32, // 'FILTROS',
            ],
            [
                'name' => 'Fastix',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Fastix alta temperatura ',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'La Gotita',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'La Gotita gel',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Pulpito',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Poxilina',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Poxipol',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Poxiran',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Unipox',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Uhu',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Ecole',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Gotita Pegamil',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Gotita Suprabond',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Unipox extra fuerte',
                'id_category' => 33, // 'Pegamentos',
            ],
            [
                'name' => 'Mamaderas',
                'id_category' => 34, // 'Mamaderas y Tetinas',
            ],
            [
                'name' => 'Chupetes anatómicos x 6u.',
                'id_category' => 34, // 'Mamaderas y Tetinas',
            ],
            [
                'name' => 'Tetinas siliconas x 10u.',
                'id_category' => 34, // 'Mamaderas y Tetinas',
            ],
            [
                'name' => 'Washintong marron/negra',
                'id_category' => 35, // 'Pomadas',
            ],
            [
                'name' => 'Peine de bolsillo x 12u',
                'id_category' => 36, // 'Peines',
            ],
            [
                'name' => 'Peine Familiar x 12u.',
                'id_category' => 36, // 'Peines',
            ],
            [
                'name' => 'Babysec',
                'id_category' => 37, // 'Pañales',
            ],
            [
                'name' => 'Huggies',
                'id_category' => 37, // 'Pañales',
            ],
            [
                'name' => 'Duracell AA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Duracell AAA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Duracell Bateria 9vt.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Duracell Mediana C',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Duracell Grande D',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Energizer AA tira 10 unid',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Energizer AAA tira 10 unid',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Energizer Mediana C',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Energizer Grande D',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Energizer Bateria 9vt.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Everready AA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Everready AAA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Everready Mediana C',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Everready Charola D',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Everready Grande D',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Everready Bateria 9vt.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Everready Tubo paq.AA ',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Everready Tubo AAA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Rayovac recarg. AA x 2u.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Rayovac recarg. AAA x 2u.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Energizzer AA blíster 4 unid.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Energizzer AAA blíster 4 unid.',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Rayovac AA tubo 4 unid.( 48)',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Rayovac AAA tubo 4 unid.( 48)',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Rayovac AAA tubo 2 unid .(30)',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Cargador pilas AA/AAA',
                'id_category' => 38, // 'Pilas (Precio por unidad)',
            ],
            [
                'name' => 'Prime super fino',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Texturado',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Lubricado',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Ultra fino',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Espermicida',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Tachas',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Mega',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Warring',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Retardante',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Anatómico',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Zero',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Sking',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Tulipan',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Prime Tulipan Excibidor surt.x 26',
                'id_category' => 39, // 'Preservativos',
            ],
            [
                'name' => 'Sedal Shampoo sobres x 24u.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
            ],
            [
                'name' => 'Sedal Crema enjuague sobres x 24u.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
            ],
            [
                'name' => 'Sedal Shampoo/ enj x 200cc.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
            ],
            [
                'name' => 'Pantene Shampoo/ enj 200cc.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
            ],
            [
                'name' => 'Pantene Shampoo sobres x 24u.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
            ],
            [
                'name' => 'Pantene Crema enj. Sobres x 24u.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
            ],
            [
                'name' => 'Shampo Dove x 200cc.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
            ],
            [
                'name' => 'Crema enjuague Dove x 200cc.',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
            ],
            [
                'name' => 'Dove shampoo/enjuague sachet x 24',
                'id_category' => 40, // 'Shamphoos(Sedal,Pantene,Dove)',
            ],
            [
                'name' => 'Efficient x 100grs.',
                'id_category' => 41, // 'Talcos',
            ],
            [
                'name' => 'Belabin x 100grs.p/cuerpo',
                'id_category' => 41, // 'Talcos',
            ],
            [
                'name' => 'Efficient Aero',
                'id_category' => 41, // 'Talcos',
            ],
            [
                'name' => 'Lumilagro  Acero Inox ',
                'id_category' => 42, // 'Termos',
            ],
            [
                'name' => 'Lumilagro x 1lt.',
                'id_category' => 42, // 'Termos',
            ],
            [
                'name' => 'Tapon termo común',
                'id_category' => 42, // 'Termos',
            ],
            [
                'name' => 'Tapon Saturno con tapita',
                'id_category' => 42, // 'Termos',
            ],
            [
                'name' => 'Mate listo Taragui',
                'id_category' => 42, // 'Termos',
            ],
            [
                'name' => 'Termo listo Taragui',
                'id_category' => 42, // 'Termos',
            ],
            [
                'name' => 'Pila esp. 1220',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. 1216',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. 2016',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. 1620',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. 2025',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. 2032',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. 2450',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. 2012',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. CR 123',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. A76 X 10',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. A23 (unidad)',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila esp. A27 (unidad)',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pilas LR 1130/LR54/AG10 X 10',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pilas LR 44 x 5unid.',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Renatta 377 x 10u.',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'LR41 x5 unid.',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pila Audifono AC13 x6 Rayovac',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Pilas 1632 Renata',
                'id_category' => 43, // 'Pilas Especiales',
            ],
            [
                'name' => 'Memoria 32 Gb',
                'id_category' => 44, // 'Memorias y pendrive',
            ],
            [
                'name' => 'Memoria 64 Gb',
                'id_category' => 44, // 'Memorias y pendrive',
            ],
            [
                'name' => 'Pendrive 32 Gb',
                'id_category' => 44, // 'Memorias y pendrive',
            ],
            [
                'name' => 'Pendrive 16 Gb',
                'id_category' => 44, // 'Memorias y pendrive',
            ],
            [
                'name' => 'Pendrive 16 Gb',
                'id_category' => 10, // '*NO VA*Novedades',
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 20u 45x60',
                'id_category' => 10, // '*NO VA*Novedades',
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 20u 50X70',
                'id_category' => 10, // '*NO VA*Novedades',
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 10u 60x90',
                'id_category' => 10, // '*NO VA*Novedades',
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 10u 80x110',
                'id_category' => 10, // '*NO VA*Novedades',
            ],
            [
                'name' => 'Virutex Bolsa res. Rollo 10u 80x110',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Doncella nocturna',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Calipso toa. Común',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Calipso toa. Nocturna',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Calipso protector',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Calipso protector cola less',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Doncella toa c/alas x8 ',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Doncella toa ultra fina',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Doncella protector',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Doncella tanga',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Lady soft toa. verde',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Lady Soft u/delgada',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Lina toa, c/alas',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Lina toa, nocturna',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Lina protect.diario',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Lina Toa.incontinencia',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Kotex toa.normal',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Kotex toa. Ultra fina antibacterial ',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Kotex toa. nocturna',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Kotex protect. Diario',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Nosotras toa normal',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Toallita Humedas Belabin x 100',
                'id_category' => 45, // 'Toallitas y Protectores',
            ],
            [
                'name' => 'Jacaranda parafina',
                'id_category' => 46, // 'Velas',
            ],
            [
                'name' => 'Bengalitas x 4u.',
                'id_category' => 46, // 'Velas',
            ],
            [
                'name' => 'Velon Cumpleaños x 20',
                'id_category' => 46, // 'Velas',
            ],
            [
                'name' => 'Tanza Bordeadora fina',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Tanza Bordeadora gruesa',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Teflon angosto',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Calorito reforzado p/termos',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Caloritos metalicos',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Escarapelas x 24',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Broches plásticos x 12 ',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Anilinas azul/negra',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Cuchillos descartables x 50 ',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Tenedores descartables x 50',
                'id_category' => 47, // 'Varios',
            ],
            [
                'name' => 'Actron 400 x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Actron Mujer x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Actron Plus',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Alergia x 10 (loratidina)',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Alikal sobres',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Amoxidal x 8',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Bucoangin caramelos x 9 ',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Amoxicilina',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Amoxicilina 875 x 7',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Amoxicilina 875 + acido clavulani',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Anaflex x 8',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Anaflex Plus',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Aspirinetas x 98 ',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => ' Bayaspirina x 30u',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Bayaspirina C fria sobres',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Bayaspirina C caliente sobres',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Buscapina x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Buscapina Perlas x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Cafiaspirina x 30',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Carbon x 10 past.',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Cefalexina 500 x 8',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Chicle Laxante',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Diclofenac 75 mg.',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Diclofenac Pridinol x 15',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Diclofenac gel pomo',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Dorixina x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Famotidina 20 mg (antiácido)x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Ibu Evanol r/accion x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Ibu Evanol Plus x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Ibu Evanol Plus x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Ibupirac x 12',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Ibuprofeno 400 x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Ibuprofeno 600x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Keterolac sublingual x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Keterolac x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Loperamida x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Migral x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Mejoral x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Next comprimidos',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Novalgina x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Qura Plus x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Paracetamol',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Paracetamol 1 gr x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Rennie x 12',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Sertal x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Sindenafil 100 mg x 1 ',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Te Vita antigripal',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Sertal Perlas x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Tabcin x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Tafirol x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Tafirol Forte x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Tafirol Plus x 8',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Omeprazol x 15',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Tafirol Duo x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Tafirol Resaca',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Tafirol Espasmo',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Te Vent 3',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Te Vick sobres',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Te Vick Forte sobres',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Te Next',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Uvasal sobres',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Viagra x 2',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Viagra masticable x 2',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Tafirol 1 mgr. X 8',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Tafirol Migra x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Refrianex x 10',
                'id_category' => 28, // 'Analgésicos',
            ],
            [
                'name' => 'Vick lata 12 grs',
                'id_category' => 28, // 'Analgésicos',
            ],
        ];

        foreach ($realProducts as $product) {
            Product::create($product);
        }

                // Create a typical product
        Product::create([
            'name' => 'Professional Laptop Stand',
            'buy_price' => 25,
            'profit_percentage' => 50,
            'sale_price' => 37,
            'current_stock' => 15,
            'min_stock_alert' => 5,
            'id_category' => 1
        ]);
    }
}