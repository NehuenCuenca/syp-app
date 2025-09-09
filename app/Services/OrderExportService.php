<?php

namespace App\Services;

use App\Models\Order;
use App\Exports\OrderExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OrderExportService
{
    /**
     * Exportar pedido a Excel
     * 
     * @param int $orderId
     * @param bool $includeHeader
     * @return string Ruta del archivo generado
     */
    public function exportOrderToExcel(int $orderId, bool $includeHeader = true, $ticketType = 'Presupuesto X'): string
    {
        // Verificar que el pedido sea exportable
        if (!$this->isOrderExportable($orderId)) {
            throw new \Exception('El pedido no cumple con los criterios para exportación');
        }

        // Obtener datos del pedido con relaciones
        $orderData = $this->getOrderData($orderId);

        // Generar nombre único para el archivo
        $fileName = $this->generateFileName($orderData['order']);

        // Crear y exportar archivo Excel
        $filePath = storage_path('app/temp/' . $fileName);
        
        // Asegurarse de que el directorio existe
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Crear el archivo Excel directamente
        Excel::store(
            new OrderExport($orderData, $includeHeader, $ticketType),
            'temp/' . $fileName,
            'local'
        );

        return $filePath;
    }

    /**
     * Verificar si un pedido es exportable
     * 
     * @param int $orderId
     * @return bool
     */
    public function isOrderExportable(int $orderId): bool
    {
        $order = Order::find($orderId);

        if (!$order) {
            return false;
        }

        return $order->order_type === 'Venta' && 
               ($order->order_status === 'Completado' || $order->order_status === 'Pendiente');
    }
    
    /**
     *  Verificar si un pedido tiene detalles de productos de una categoría determinada
     * 
     * @param int $orderId
     * @return bool
     */
    public function hasCategory(int $orderId, int $categoryId): bool
    {
        $order = Order::find($orderId);
        
        // Verificar si el pedido tiene detalles de productos de la categoría
        $hasCategory = $order->orderDetails()->whereHas('product', function ($query) use ($categoryId) {
            $query->where('id_category', $categoryId);
        })->exists();

        return $hasCategory;
    }

    /**
     * Obtener datos completos del pedido
     * 
     * @param int $orderId
     * @return array
     */
    private function getOrderData(int $orderId): array
    {
        // Obtener datos del pedido con relaciones
        $order = Order::with(['contact', 'userCreator'])
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            throw new \Exception('Pedido no encontrado');
        }

        // Obtener detalles del pedido con productos
        $orderDetails = DB::table('order_details as od')
            ->join('products as p', 'od.id_product', '=', 'p.id')
            ->where('od.id_order', $orderId)
            ->select([
                'od.id',
                'od.quantity',
                'od.unit_price_at_order',
                'od.discount_percentage_by_unit',
                'od.line_subtotal',
                'p.sku',
                'p.name as product_name'
            ])
            ->get();

        return [
            'order' => $order,
            'order_details' => $orderDetails
        ];
    }

    /**
     * Generar nombre único para el archivo
     * 
     * @param Order $order
     * @return string
     */
    private function generateFileName(Order $order): string
    {
        $date = $order->created_at->format('Y-m-d');
        $orderId = str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $contact = str_replace(' ', '_', $order->contact->company_name);
        return "boleta_pedido_{$contact}_{$orderId}_{$date}_" . time() . ".xlsx";
    }

    public function getTicketTypes(): array
    {
        return [
            'PRESUPUESTO X',
            'BOLETA DE VENTA',
        ];
    }
}