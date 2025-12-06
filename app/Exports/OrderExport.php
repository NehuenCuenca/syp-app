<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrderExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, ShouldAutoSize, WithEvents
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
        $this->headerRowsCount = $includeHeader ? 7 : 2; // 7 filas para el encabezado
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
            'COD',
            'Producto', 
            'Cantidad',
            'Precio unidad',
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
            $row->code,
            $row->product_name,
            number_format($row->quantity, 0),
            '$' . number_format($row->unit_price, 0),
            number_format(($row->percentage_applied), 0) . '%',
            '$' . number_format($row->line_subtotal, 0)
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

            $sheet->setCellValue("A{$currentRow}", 'Codigo pedido:');
            $sheet->setCellValue("B{$currentRow}", $order->code);
            $currentRow++;

            $sheet->setCellValue("A{$currentRow}", 'Comprador:');
            $comprador = $order->contact ? "{$order->contact->company_name}" : 'N/A';
            $sheet->setCellValue("B{$currentRow}", $comprador);
            $currentRow++;

            $sheet->setCellValue("A{$currentRow}", 'Vendedor:');
            // $vendedor = $order->userCreator ? "{$order->userCreator->username} (TEL:{$order->userCreator->phone})" : 'N/A';
            $admin = User::firstWhere('email', 'sergioross73@hotmail.com');
            $vendedor = $admin ? "{$admin->username} (TEL:{$admin->phone})" : "Sofia Distribuciones";
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
        
        
        $subTotalRow = $tableDataEndRow + 3;
        $adjustmentRow = $tableDataEndRow + 4;
        $totalRow = $tableDataEndRow + 5; // Fila del total
        
        // Agregar fila de subtotal
        $sheet->setCellValue("E{$subTotalRow}", 'SUBTOTAL:');
        $sheet->setCellValue("F{$subTotalRow}", '$' . number_format($order->subtotal, 0));
        
        $currentRow++;

        // Agregar fila de ajuste
        $sheet->setCellValue("E{$adjustmentRow}", 'AJUSTE:');
        $sheet->setCellValue("F{$adjustmentRow}", '$' . number_format($order->adjustment_amount, 0));
        
        $currentRow++;

        // Agregar fila de total
        $sheet->setCellValue("E{$totalRow}", 'TOTAL NETO:');
        $sheet->setCellValue("F{$totalRow}", '$' . number_format($order->total_net, 0));

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
            $styles['A3:A6'] = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF0F8FF']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ]
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

        // Fila del subtotal
        $styles["E{$subTotalRow}:F{$subTotalRow}"] = [
            'font' => [
                // 'bold' => true,
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFEB9C']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ]
        ];

        // Fila del ajuste
        $styles["E{$adjustmentRow}:F{$adjustmentRow}"] = [
            'font' => [
                // 'bold' => true,
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFEB9C']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ]
        ];

        // Fila del total
        $styles["E{$totalRow}:F{$totalRow}"] = [
            'font' => [
                'bold' => true,
                'size' => 18
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFEB9C']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ]
        ];

        return $styles;
    }

    /**
     * Registra eventos para proteger el archivo
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // OPCIÓN 1: Proteger toda la hoja con contraseña
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword($this->orderData['order']->code);
                
                // OPCIÓN 2: Proteger elementos específicos
                $sheet->getProtection()->setSort(true);           // No permitir ordenar
                $sheet->getProtection()->setInsertRows(true);     // No permitir insertar filas
                $sheet->getProtection()->setInsertColumns(true);  // No permitir insertar columnas
                $sheet->getProtection()->setDeleteRows(true);     // No permitir eliminar filas
                $sheet->getProtection()->setDeleteColumns(true);  // No permitir eliminar columnas
                $sheet->getProtection()->setFormatCells(true);    // No permitir formatear celdas
                $sheet->getProtection()->setFormatColumns(true);  // No permitir formatear columnas
                $sheet->getProtection()->setFormatRows(true);     // No permitir formatear filas
                $sheet->getProtection()->setSelectLockedCells(true);   // Permitir seleccionar celdas bloqueadas
                $sheet->getProtection()->setSelectUnlockedCells(true); // Permitir seleccionar celdas desbloqueadas
            },
        ];
    }
}