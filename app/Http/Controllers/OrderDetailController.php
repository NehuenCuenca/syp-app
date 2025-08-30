<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Http\Requests\StoreOrderDetailRequest;
use App\Http\Requests\UpdateOrderDetailRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->has('id_order')){
                $query = OrderDetail::where('id_order', $request->id_order)->get();
            } else {
                $query = OrderDetail::all();
            }
            return response()->json([
                'success' => true,
                'message' => 'Detalles recuperados exitosamente.',
                'total' => $query->count(),
                'details' => $query,
                'specified_order' => ($request->has('id_order')) ? Order::find($request->id_order) : null
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Error al recuperar los detalles.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderDetailRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $validatedData = $request->validated();
            $orderData = $validatedData['order_detail'];
            $orderId = $validatedData['id_order'];
            
            // Verificar que el pedido existe y está en estado 'Pendiente'
            $order = Order::findOrFail($orderId);
            
            if ($order->order_status !== 'Pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden añadir detalles a pedidos en estado Pendiente'
                ], 400);
            }
            
            // Verificar disponibilidad de stock para ventas salientes
            if ($order->order_type === 'Venta') {
                $product = Product::findOrFail($orderData['id_product']);
                
                if ($product->current_stock < $orderData['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente. Stock disponible: ' . $product->current_stock
                    ], 400);
                }
            }
            
            // Calcular subtotal
            $lineSubtotal = $orderData['quantity'] * $orderData['unit_price_at_order'];
            
            // Crear el detalle del pedido
            $orderDetail = OrderDetail::create([
                'id_order' => $orderId,
                'id_product' => $orderData['id_product'],
                'quantity' => $orderData['quantity'],
                'unit_price_at_order' => $orderData['unit_price_at_order'],
                'line_subtotal' => $lineSubtotal,
                'discount_percentage_by_unit' => $orderData['discount_percentage_by_unit'] ?? 0,
            ]);
            
            // Manejar stock y movimientos para ventas salientes
            if ($order->order_type === 'Venta') {
                // Descontar stock
                $product = Product::findOrFail($orderData['id_product']);
                $product->decrement('current_stock', $orderData['quantity']);
                
                // Registrar movimiento de stock
                StockMovement::create([
                    'id_product' => $orderData['id_product'],
                    'id_order' => $orderId,
                    'id_user_responsible' => Auth::id(),
                    'movement_type' => 'Venta',
                    'quantity_moved' => -$orderData['quantity'], // Negativo para salida
                    'movement_date' => now(),
                    'notes' => 'Descuento de stock por detalle de pedido #' . $order->id
                ]);
            }
            
            // Actualizar totales del pedido
            $this->updateOrderTotals($orderId);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $orderDetail->load(['order', 'product']),
                'message' => 'Detalle de pedido creado exitosamente'
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el detalle del pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderDetail $orderDetail)
    {
        return response()->json([
            'success' => true,
            'data' => $orderDetail->load(['order', 'product']),
            'message' => 'Detalle de pedido obtenido exitosamente'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderDetailRequest $request, OrderDetail $orderDetail)
    {
        try {
            DB::beginTransaction();
            
            $validatedData = $request->validated();
            $newData = $validatedData['order_detail'];
            
            // Verificar que el pedido está en estado 'Pendiente'
            $order = $orderDetail->order;
            
            if ($order->order_status !== 'Pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden editar detalles de pedidos en estado Pendiente'
                ], 400);
            }
            
            // Verificar si hay cambios en producto o cantidad
            $productChanged = $orderDetail->id_product != $newData['id_product'];
            $quantityChanged = $orderDetail->quantity != $newData['quantity'];
            $unitPriceChanged = $orderDetail->unit_price_at_order != $newData['unit_price_at_order'];

            if (!$productChanged && !$quantityChanged && !$unitPriceChanged) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se detectaron cambios en el producto, la cantidad o el precio unitario'
                ], 400);
            }
            
            // Manejar cambios para ventas salientes
            if ($order->order_type === 'Venta') {
                
                if ($productChanged) {
                    // Revertir stock del producto anterior
                    $oldProduct = Product::findOrFail($orderDetail->id_product);
                    $oldProduct->increment('current_stock', $orderDetail->quantity);
                    
                    // Eliminar movimiento de stock anterior
                    StockMovement::where('id_order', $order->id)
                        ->where('id_product', $orderDetail->id_product)
                        ->where('movement_type', 'Venta')
                        ->where('quantity_moved', -$orderDetail->quantity)
                        ->delete();
                    
                    // Verificar stock del nuevo producto
                    $newProduct = Product::findOrFail($newData['id_product']);
                    if ($newProduct->current_stock < $newData['quantity']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Stock insuficiente para el nuevo producto. Stock disponible: ' . $newProduct->current_stock
                        ], 400);
                    }
                    
                    // Descontar stock del nuevo producto
                    $newProduct->decrement('current_stock', $newData['quantity']);
                    
                    // Registrar nuevo movimiento de stock
                    StockMovement::create([
                        'id_product' => $newData['id_product'],
                        'id_order' => $order->id,
                        'id_user_responsible' => Auth::id(),
                        'movement_type' => 'Venta',
                        'quantity_moved' => -$newData['quantity'],
                        'movement_date' => now(),
                        'notes' => 'Descuento de stock por actualización de detalle de pedido #' . $order->id
                    ]);
                    
                } elseif ($quantityChanged) {
                    // Solo cambió la cantidad
                    $product = Product::findOrFail($orderDetail->id_product);
                    $quantityDifference = $newData['quantity'] - $orderDetail->quantity;
                    
                    // Verificar si hay stock suficiente para el aumento
                    if ($quantityDifference > 0 && $product->current_stock < $quantityDifference) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Stock insuficiente para el aumento de cantidad. Stock disponible: ' . $product->current_stock
                        ], 400);
                    }
                    
                    // Actualizar stock
                    $product->decrement('current_stock', $quantityDifference);
                    
                    // Actualizar movimiento de stock existente
                    $stockMovement = StockMovement::where('id_order', $order->id)
                        ->where('id_product', $orderDetail->id_product)
                        ->where('movement_type', 'Venta')
                        ->first();
                    
                    if ($stockMovement) {
                        $stockMovement->update([
                            'quantity_moved' => -$newData['quantity'],
                            'notes' => 'Actualización de cantidad en detalle de pedido #' . $order->id
                        ]);
                    }
                }
            }
            
            // Actualizar el detalle del pedido
            $lineSubtotal = $newData['quantity'] * $newData['unit_price_at_order'];
            
            $orderDetail->update([
                'id_product' => $newData['id_product'],
                'quantity' => $newData['quantity'],
                'unit_price_at_order' => $newData['unit_price_at_order'],
                'line_subtotal' => $lineSubtotal,
                'discount_percentage_by_unit' => $newData['discount_percentage_by_unit'],
            ]);
            
            // Actualizar totales del pedido
            $this->updateOrderTotals($order->id);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $orderDetail->load(['order', 'product']),
                'message' => 'Detalle de pedido actualizado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el detalle del pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderDetail $orderDetail)
    {
        try {
            DB::beginTransaction();
            
            // Verificar que el pedido está en estado 'Pendiente'
            $order = $orderDetail->order;
            
            if ($order->order_status !== 'Pendiente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden eliminar detalles de pedidos en estado Pendiente'
                ], 400);
            }
            
            // Manejar reversión de stock para ventas salientes
            if ($order->order_type === 'Venta') {
                // Revertir stock
                $product = Product::findOrFail($orderDetail->id_product);
                $product->increment('current_stock', $orderDetail->quantity);
                
                // Eliminar movimiento de stock
                StockMovement::where('id_order', $order->id)
                    ->where('id_product', $orderDetail->id_product)
                    ->where('movement_type', 'Venta')
                    ->where('quantity_moved', -$orderDetail->quantity)
                    ->delete();
            }
            
            // Eliminar el detalle del pedido
            $orderDetail->delete();
            
            // Actualizar totales del pedido
            $this->updateOrderTotals($order->id);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Detalle de pedido eliminado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el detalle del pedido: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar los totales del pedido
     */
    private function updateOrderTotals($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $totalGross = $order->orderDetails()->sum('line_subtotal');
        $totalNet = $totalGross;
        
        $order->update(['total_net' => $totalNet]);
    }
}