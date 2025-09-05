<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Services\OrderExportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderExportController extends Controller
{
    protected OrderExportService $orderExportService;

    public function __construct(OrderExportService $orderExportService)
    {
        $this->orderExportService = $orderExportService;
    }

    /**
     * Exportar pedido a Excel
     * 
     * @param Request $request
     * @param int $orderId
     * @return BinaryFileResponse|JsonResponse
     */
    public function exportOrderToExcel(Request $request, int $orderId): BinaryFileResponse|JsonResponse
    {
        try {
            // Validar parámetros de entrada
            $request->validate([
                'include_header' => 'boolean', // Toggle para incluir encabezado
                'ticket_type' => 'string', // Tipo de ticket
            ]);

            $includeHeader = $request->boolean('include_header', true);
            $ticketType = $request->string('ticket_type', 'PRESUPUESTO X');
            // Generar archivo Excel usando el servicio
            $filePath = $this->orderExportService->exportOrderToExcel($orderId, $includeHeader, $ticketType);

            // Retornar archivo para descarga
            
            return response()->download($filePath, null, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);


        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el archivo Excel',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un pedido es exportable
     * 
     * @param int $orderId
     * @return JsonResponse
     */
    public function checkOrderExportability(int $orderId): JsonResponse
    {
        try {
            $isExportable = $this->orderExportService->isOrderExportable($orderId);
            
            return response()->json([
                'is_exportable' => $isExportable,
                'message' => $isExportable 
                    ? 'El pedido puede ser exportado' 
                    : 'El pedido no cumple con los criterios para exportación',
                'ticket_types' => $this->orderExportService->getTicketTypes()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al verificar el pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}