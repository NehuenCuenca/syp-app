<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class CatalogExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $categories;

    public function __construct()
    {
        // Cargar categorías con sus productos
        $this->categories = Category::with('products')->get();
    }

    /**
     * Retorna la colección de datos para el Excel
     */
    public function collection()
    {
        $rows = new Collection();

        foreach ($this->categories as $category) {
            // Fila de categoría (search_alias de categoría en la primera columna)
            $rows->push([
                $category->search_alias,
                ''
            ]);

            // Filas de productos de esta categoría
            foreach ($category->products as $product) {
                $rows->push([
                    '    ' . $product->search_alias, // Indentación para diferenciación visual
                    $product->sale_price
                ]);
            }

            // Fila vacía como separador entre categorías
            $rows->push(['', '']);
        }

        return $rows;
    }

    /**
     * Define los encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'Productos por Categoria',
            'Precio de Venta'
        ];
    }

    /**
     * Eventos para aplicar estilos al Excel
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Aplicar negrita a los encabezados
                $sheet->getStyle('A1:B1')->getFont()->setBold(true);
                
                // Aplicar negrita a las filas de categorías
                $rowNumber = 2; // Comenzamos después del encabezado
                
                foreach ($this->categories as $category) {
                    // Aplicar negrita a la fila de categoría
                    $sheet->getStyle("A{$rowNumber}:B{$rowNumber}")->getFont()->setBold(true);
                    
                    // Aplicar color de fondo gris claro a la categoría
                    $sheet->getStyle("A{$rowNumber}:B{$rowNumber}")
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFE0E0E0');
                    
                    // Avanzar el número de fila: 1 para categoría + cantidad de productos + 1 línea vacía
                    $rowNumber += 1 + $category->products->count() + 1;
                }

                // Aplicar formato de moneda a la columna de precio
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("B2:B{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');
                
                // Aplicar bordes a toda la tabla
                $sheet->getStyle("A1:B{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}