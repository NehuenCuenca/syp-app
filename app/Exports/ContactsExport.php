<?php

namespace App\Exports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class ContactsExport implements FromCollection, WithStyles, WithColumnWidths, WithEvents
{
    protected $groupTitleRows = [];
    protected $contactRows = [];
    protected $headerRow = 1;

    /**
     * Retorna la colección de datos que se exportarán
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Obtener todos los contactos ordenados por tipo
        $contacts = Contact::orderBy('contact_type')
            ->orderBy('code')
            ->get();

        // Agrupar contactos por tipo
        $groupedContacts = $contacts->groupBy('contact_type');

        $rows = new Collection();
        $currentRow = 1;

        // Fila 1: Agregar encabezado
        $rows->push(['Contacto / Tipo', 'Telefono']);
        $this->headerRow = $currentRow;
        $currentRow++;

        $isFirstGroup = true;

        // Iterar por cada grupo de contactos
        foreach ($groupedContacts as $contactType => $contactsInGroup) {
            
            // Si NO es el primer grupo, agregar fila en blanco ANTES del título
            if (!$isFirstGroup) {
                $rows->push(['', '']); // Fila vacía
                $currentRow++;
            }
            
            // Determinar el nombre del grupo en plural
            $groupName = $this->getGroupName($contactType);
            
            // Guardar el número de fila del título del grupo
            $this->groupTitleRows[] = $currentRow;
            
            // Agregar título del grupo (solo en columna A, columna B vacía)
            $rows->push([$groupName, '']);
            $currentRow++;

            // Agregar cada contacto del grupo
            foreach ($contactsInGroup as $contact) {
                $this->contactRows[] = $currentRow; // Guardar filas de contactos
                $rows->push([
                    $contact->search_alias,
                    $contact->phone_number_info
                ]);
                $currentRow++;
            }

            $isFirstGroup = false;
        }

        return $rows;
    }

    /**
     * Obtiene el nombre del grupo en plural
     *
     * @param string $contactType
     * @return string
     */
    protected function getGroupName($contactType)
    {
        $names = [
            'Cliente' => 'Clientes',
            'Proveedor' => 'Proveedores',
        ];

        return $names[$contactType] ?? $contactType . 's';
    }

    /**
     * Define los estilos de las celdas
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $styles = [];

        // Estilo para el encabezado principal (fila 1) - SIN fondo gris
        $styles[$this->headerRow] = [
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Estilos para los títulos de los grupos (fuente 20, negrita, fondo gris)
        foreach ($this->groupTitleRows as $rowNumber) {
            $styles[$rowNumber] = [
                'font' => [
                    'bold' => true,
                    'size' => 20,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E0E0E0',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ];
        }

        return $styles;
    }

    /**
     * Define el ancho de las columnas
     *
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 20,
        ];
    }

    /**
     * Registra eventos para aplicar estilos adicionales
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Aplicar bordes ligeros a todas las celdas
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC'],
                            ],
                        ],
                    ]);

                // Alinear texto a la izquierda en todas las celdas
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Aplicar fondo gris a AMBAS columnas de los títulos de grupo
                foreach ($this->groupTitleRows as $groupRow) {
                    $sheet->getStyle('A' . $groupRow . ':B' . $groupRow)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('E0E0E0');
                }

                // Aplicar tamaño de fuente 18 a las filas de contactos
                foreach ($this->contactRows as $contactRow) {
                    $sheet->getStyle('A' . $contactRow . ':B' . $contactRow)
                        ->getFont()
                        ->setSize(18);
                }

                // Aplicar saltos de página después de cada grupo
                foreach ($this->groupTitleRows as $index => $groupRow) {
                    // Si no es el último grupo
                    if ($index < count($this->groupTitleRows) - 1) {
                        // El siguiente grupo empieza en groupTitleRows[$index + 1]
                        // La fila vacía está en groupTitleRows[$index + 1] - 1
                        // El último contacto del grupo actual está en groupTitleRows[$index + 1] - 2
                        $lastContactRow = $this->groupTitleRows[$index + 1] - 2;
                        
                        // Aplicar salto de página después del último contacto del grupo
                        $sheet->setBreak(
                            'A' . ($lastContactRow + 1), 
                            Worksheet::BREAK_ROW
                        );
                    }
                }

                // Ajustar altura de filas automáticamente
                for ($i = 1; $i <= $highestRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(-1);
                }
            },
        ];
    }
}