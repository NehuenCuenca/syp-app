<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\OrderExportService;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderExportController extends Controller
{
    use ApiResponseTrait;
    
    protected OrderExportService $orderExportService;

    public function __construct(OrderExportService $orderExportService)
    {
        $this->orderExportService = $orderExportService;
    }

    /**
     * Exportar pedido a Excel
     * 
     * @param Request $request
     * @param Order $order
     * @return BinaryFileResponse|JsonResponse
     */
    public function exportOrderToExcel(Request $request, Order $order): BinaryFileResponse|JsonResponse
    {
        try {
            // Validar parámetros de entrada
            $validatedData = $request->validate([
                'include_header' => 'boolean', // Toggle para incluir encabezado
                'ticket_type' => 'string', // Tipo de ticket
            ]);

            $includeHeader = $request->boolean('include_header', true);
            $ticketType = $request->string('ticket_type', 'PRESUPUESTO X');

            // Verificar si el pedido existe y es válido
            if (!$this->orderExportService->isOrderExportable($order->id)) {
                return $this->errorResponse(
                    'El pedido no cumple con los criterios para exportación',
                    [],
                    ['order_id' => $order->id],
                    422
                );
            }

            // Get if an order has details of products belonging to a certain category
            $specialCategory = Category::where('name', 'Analgésicos')->first();
            
            if (!$specialCategory) {
                return $this->errorResponse(
                    'No se pudo encontrar la categoría especial requerida',
                    ['category' => 'Categoría "Analgésicos" no encontrada'],
                    ['order_id' => $order->id],
                    500
                );
            }

            $hasSpecialCategory = $this->orderExportService->hasCategory($order->id, $specialCategory->id);
            $includeHeader = ($hasSpecialCategory) ? false : $includeHeader;

            // Generar archivo Excel usando el servicio
            $filePath = $this->orderExportService->exportOrderToExcel($order->id, $includeHeader, $ticketType);

            // Verificar que el archivo se generó correctamente
            if (!$filePath || !file_exists($filePath)) {
                return $this->errorResponse(
                    'Error al generar el archivo Excel',
                    ['file' => 'No se pudo crear el archivo de exportación'],
                    ['order_id' => $order->id],
                    500
                );
            }

            // Retornar archivo para descarga
            return response()->download($filePath, null, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Filename' => $this->orderExportService->generateFileName($order)
            ])->deleteFileAfterSend(true);

        } catch (ValidationException $e) {
            return $this->validationErrorResponse(
                $e->errors(),
                'Error de validación en los parámetros de entrada'
            );
            
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(
                'El pedido solicitado no fue encontrado'
            );
            
        } catch (Exception $e) {
            // Log del error para debugging
            Log::error('Error en exportOrderToExcel', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Error interno del servidor al generar el archivo Excel',
                ['exception' => $e->getMessage()],
                ['order_id' => $order->id],
                500
            );
        }
    }

    /**
     * Verificar si un pedido es exportable
     * 
     * @param Order $order
     * @return JsonResponse
     */
    public function checkOrderExportability(Order $order): JsonResponse
    {
        try {
            // Verificar que el ID del pedido sea válido
            if ($order->id <= 0) {
                return $this->errorResponse(
                    'ID de pedido inválido',
                    ['order_id' => 'El ID del pedido debe ser un número positivo'],
                    ['provided_id' => $order->id],
                    400
                );
            }

            $isExportable = $this->orderExportService->isOrderExportable($order->id);
            $ticketTypes = $this->orderExportService->getTicketTypes();

            $responseData = [
                'is_exportable' => $isExportable,
                'ticket_types' => $ticketTypes,
                'order_id' => $order->id
            ];

            $message = $isExportable 
                ? 'El pedido puede ser exportado' 
                : 'El pedido no cumple con los criterios para exportación';

            if ($isExportable) {
                return $this->successResponse(
                    $responseData,
                    $message,
                    ['checked_at' => now()->toISOString()]
                );
            } else {
                return $this->errorResponse(
                    $message,
                    ['exportability' => 'Pedido no cumple criterios de exportación'],
                    $responseData,
                    422
                );
            }

        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(
                'El pedido solicitado no fue encontrado'
            );
            
        } catch (Exception $e) {
            // Log del error para debugging
            Log::error('Error en checkOrderExportability', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Error interno del servidor al verificar la exportabilidad del pedido',
                ['exception' => $e->getMessage()],
                ['order_id' => $order->id],
                500
            );
        }
    }

    /**
     * Obtener tipos de ticket disponibles
     * 
     * @return JsonResponse
     */
    public function getAvailableTicketTypes(): JsonResponse
    {
        try {
            $ticketTypes = $this->orderExportService->getTicketTypes();

            return $this->successResponse(
                ['ticket_types' => $ticketTypes],
                'Tipos de ticket obtenidos exitosamente',
                ['total_types' => count($ticketTypes)]
            );

        } catch (Exception $e) {
            // Log del error para debugging
            Log::error('Error en getAvailableTicketTypes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Error interno del servidor al obtener los tipos de ticket',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }
}