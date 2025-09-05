<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrderExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize
{
    private array $orderData;
    private bool $includeHeader;
    private string $ticketType;
    private int $headerRowsCount = 0;

    public function __construct(array $orderData, bool $includeHeader = true, string $ticketType = 'PRESUPUESTO X')
    {
        $this->orderData = $orderData;
        $this->includeHeader = $includeHeader;
        $this->ticketType = $ticketType;
        $this->headerRowsCount = $includeHeader ? 6 : 2; // 6 filas para el encabezado
    }

    /**
     * Retorna la colección de datos (detalles del pedido)
     */
    public function collection()
    {
        return collect($this->orderData['order_details']);
    }

    /**
     * Define los encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'SKU',
            'Producto', 
            'Cantidad',
            'Precio Unitario',
            'Descuento (%)',
            'Subtotal'
        ];
    }

    /**
     * Mapea cada fila de datos
     */
    public function map($row): array
    {
        return [
            $row->sku,
            $row->product_name,
            number_format($row->quantity, 0),
            '$' . number_format($row->unit_price_at_order, 2, ',', '.'),
            number_format(($row->discount_percentage_by_unit*100), 0) . '%',
            '$' . number_format($row->line_subtotal, 2, ',', '.')
        ];
    }

    /**
     * Define la celda de inicio para la tabla de datos
     */
    public function startCell(): string
    {
        $startRow = $this->headerRowsCount + 1;
        return "A{$startRow}";
    }

    /**
     * Aplicar estilos y agregar datos del encabezado
     */
    public function styles(Worksheet $sheet)
    {
        $order = $this->orderData['order'];
        $orderDetails = $this->orderData['order_details'];
        $currentRow = 1;
        // Título principal
        $sheet->setCellValue('A1', $this->ticketType);
        $sheet->mergeCells('A1:F1');

        // Agregar encabezado si está habilitado
        if ($this->includeHeader) {
            $currentRow = 3; // Saltar una fila

            // Información del encabezado
            $sheet->setCellValue("A{$currentRow}", 'Fecha:');
            $sheet->setCellValue("B{$currentRow}", $order->created_at->format('d/m/Y H:i'));
            $currentRow++;

            $sheet->setCellValue("A{$currentRow}", 'Comprador:');
            $comprador = $order->contact ? "{$order->contact->company_name}" : 'N/A';
            $sheet->setCellValue("B{$currentRow}", $comprador);
            $currentRow++;

            $sheet->setCellValue("A{$currentRow}", 'Vendedor:');
            $vendedor = $order->userCreator ? "{$order->userCreator->username} (TEL:{$order->userCreator->phone})" : 'N/A';
            $sheet->setCellValue("B{$currentRow}", $vendedor);    

            $currentRow += 2; // Saltar filas antes de la tabla
        } else {
            $currentRow = 3;
        }
        

        // La tabla de datos comienza en $currentRow
        $tableStartRow = $currentRow;
        $tableHeaderRow = $tableStartRow;
        $tableDataStartRow = $tableStartRow + 1;
        $tableDataEndRow = $tableDataStartRow + count($orderDetails) - 1;
        
        // Fila del total
        $totalRow = $tableDataEndRow + 2;
        
        // Agregar fila de total
        $sheet->setCellValue("E{$totalRow}", 'TOTAL NETO:');
        $sheet->setCellValue("F{$totalRow}", '$' . number_format($order->total_net, 2, ',', '.'));

        // APLICAR ESTILOS
        $styles = [];

            // Título principal
        $styles['A1:F1'] = [
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE6E6E6']
            ]
        ];

        // Estilos del encabezado si está incluido
        if ($this->includeHeader) { 
            // Información del encabezado
            $styles['A3:A5'] = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF0F8FF']
                ]
            ];
        }

        // Encabezados de la tabla
        $styles["A{$tableHeaderRow}:F{$tableHeaderRow}"] = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ]
        ];

        // Datos de la tabla
        if ($tableDataEndRow >= $tableDataStartRow) {
            $styles["A{$tableDataStartRow}:F{$tableDataEndRow}"] = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ];

            // Columnas de números alineadas a la derecha
            $styles["D{$tableDataStartRow}:F{$tableDataEndRow}"] = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ]
            ];
        }

        // Fila del total
        $styles["E{$totalRow}:F{$totalRow}"] = [
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFEB9C']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THICK,
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ]
        ];

        return $styles;
    }
}