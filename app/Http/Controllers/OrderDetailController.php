<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Http\Requests\StoreOrderDetailRequest;
use App\Http\Requests\UpdateOrderDetailRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\MovementType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderDetailController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = null;
            $specifiedOrder = null;
            
            if ($request->has('id_order')) {
                // Validar que el pedido existe
                $specifiedOrder = Order::find($request->id_order);
                if (!$specifiedOrder) {
                    return $this->notFoundResponse('El pedido especificado no existe');
                }
                
                $query = OrderDetail::where('id_order', $request->id_order)
                    ->with(['product', 'order'])
                    ->get();
            } else {
                $query = OrderDetail::with(['product', 'order'])->get();
            }

            $responseData = [
                'total' => $query->count(),
                'details' => $query,
                'specified_order' => $specifiedOrder
            ];

            return $this->successResponse(
                $responseData,
                'Detalles recuperados exitosamente'
            );

        } catch (QueryException $e) {
            Log::error('Error de base de datos en OrderDetailController@index', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error al recuperar los detalles de la base de datos',
                ['database_error' => 'Error de consulta a la base de datos'],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

        } catch (Exception $e) {
            Log::error('Error inesperado en OrderDetailController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error inesperado al recuperar los detalles',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderDetailRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $validatedData = $request->validated();
            $orderData = $validatedData['order_detail'];
            $orderId = $validatedData['id_order'];
            
            // Verificar que el pedido existe y está en estado 'Pendiente'
            $order = Order::find($orderId);
            if (!$order) {
                DB::rollBack();
                return $this->notFoundResponse('El pedido especificado no existe');
            }
            
            if ($order->order_status !== 'Pendiente') {
                DB::rollBack();
                return $this->errorResponse(
                    'Solo se pueden añadir detalles a pedidos en estado Pendiente',
                    ['order_status' => 'Estado de pedido inválido'],
                    ['current_status' => $order->order_status]
                );
            }
            
            // Verificar que el producto existe
            $product = Product::find($orderData['id_product']);
            if (!$product) {
                DB::rollBack();
                return $this->notFoundResponse('El producto especificado no existe');
            }
            
            // Verificar disponibilidad de stock para ventas salientes
            if ($order->order_type === 'Venta') {
                if ($product->current_stock < $orderData['quantity']) {
                    DB::rollBack();
                    return $this->errorResponse(
                        'Stock insuficiente',
                        ['stock_error' => 'Cantidad solicitada excede el stock disponible'],
                        [
                            'requested_quantity' => $orderData['quantity'],
                            'available_stock' => $product->current_stock
                        ]
                    );
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
                try {
                    // Descontar stock
                    $product->decrement('current_stock', $orderData['quantity']);
                    
                    // Obtener el tipo de movimiento
                    $movementType = MovementType::where('name', 'Venta')->first();
                    if (!$movementType) {
                        throw new Exception('Tipo de movimiento "Venta" no encontrado');
                    }
                    
                    // Registrar movimiento de stock
                    StockMovement::create([
                        'id_product' => $orderData['id_product'],
                        'id_order' => $orderId,
                        'id_user_responsible' => Auth::id(),
                        'id_movement_type' => $movementType->id,
                        'quantity_moved' => -$orderData['quantity'], // Negativo para salida
                        'movement_date' => now(),
                        'notes' => 'Descuento de stock por detalle de pedido #' . $order->id
                    ]);
                    
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Error al manejar stock en OrderDetailController@store', [
                        'error' => $e->getMessage(),
                        'product_id' => $orderData['id_product'],
                        'order_id' => $orderId
                    ]);
                    
                    return $this->errorResponse(
                        'Error al procesar el movimiento de stock',
                        ['exception' => $e->getMessage()],
                        [],
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            }
            
            // Actualizar totales del pedido
            $this->updateOrderTotals($orderId);
            
            DB::commit();
            
            return $this->createdResponse(
                $orderDetail->load(['order', 'product']),
                'Detalle de pedido creado exitosamente'
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error de base de datos en OrderDetailController@store', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error de base de datos al crear el detalle del pedido',
                ['database_error' => 'Error en la operación de base de datos'],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado en OrderDetailController@store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error inesperado al crear el detalle del pedido',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderDetail $orderDetail)
    {
        try {
            $orderDetail->load(['order', 'product']);
            
            return $this->successResponse(
                $orderDetail,
                'Detalle de pedido obtenido exitosamente'
            );
            
        } catch (Exception $e) {
            Log::error('Error inesperado en OrderDetailController@show', [
                'error' => $e->getMessage(),
                'order_detail_id' => $orderDetail->id ?? null
            ]);
            
            return $this->errorResponse(
                'Error al obtener el detalle del pedido',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderDetailRequest $request, OrderDetail $orderDetail)
    {
        DB::beginTransaction();
        
        try {
            $validatedData = [
                'id_product' => $request->validated('id_product', $orderDetail->id_product),
                'quantity' => $request->validated('quantity', $orderDetail->quantity),
                'unit_price_at_order' => $request->validated('unit_price_at_order', $orderDetail->unit_price_at_order),
                'discount_percentage_by_unit' => $request->validated('discount_percentage_by_unit', $orderDetail->discount_percentage_by_unit),
            ];
            
            // Verificar que el pedido está en estado 'Pendiente'
            $order = $orderDetail->order;
            if (!$order) {
                DB::rollBack();
                return $this->notFoundResponse('Pedido asociado no encontrado');
            }
            
            if ($order->order_status !== 'Pendiente') {
                DB::rollBack();
                return $this->errorResponse(
                    'Solo se pueden editar detalles de pedidos en estado Pendiente',
                    ['order_status' => 'Estado de pedido inválido'],
                    ['current_status' => $order->order_status]
                );
            }
            
            // Verificar que el nuevo producto existe (si cambió)
            if (isset($validatedData['id_product']) && $validatedData['id_product'] != $orderDetail->id_product) {
                $newProduct = Product::find($validatedData['id_product']);
                if (!$newProduct) {
                    DB::rollBack();
                    return $this->notFoundResponse('El nuevo producto especificado no existe');
                }
            }
            
            // Verificar si hay cambios en producto, cantidad o precio
            $productChanged = $orderDetail->id_product != $validatedData['id_product'];
            $quantityChanged = $orderDetail->quantity != $validatedData['quantity'];
            $unitPriceChanged = $orderDetail->unit_price_at_order != $validatedData['unit_price_at_order'];
            $discountChanged = ($orderDetail->discount_percentage_by_unit ?? 0) != ($validatedData['discount_percentage_by_unit'] ?? 0);

            if (!$productChanged && !$quantityChanged && !$unitPriceChanged && !$discountChanged) {
                DB::rollBack();
                return $this->errorResponse(
                    'No se detectaron cambios en el detalle del pedido',
                    ['no_changes' => 'Los datos enviados son idénticos a los actuales']
                );
            }
            
            // Manejar cambios para ventas salientes
            if ($order->order_type === 'Venta') {
                try {
                    $movementType = MovementType::where('name', 'Venta')->first();
                    if (!$movementType) {
                        throw new Exception('Tipo de movimiento "Venta" no encontrado');
                    }
                    
                    if ($productChanged) {
                        // Revertir stock del producto anterior
                        $oldProduct = Product::find($orderDetail->id_product);
                        if ($oldProduct) {
                            $oldProduct->increment('current_stock', $orderDetail->quantity);
                        }
                        
                        // Eliminar movimiento de stock anterior
                        StockMovement::where('id_order', $order->id)
                            ->where('id_product', $orderDetail->id_product)
                            ->where('id_movement_type', $movementType->id)
                            ->where('quantity_moved', -$orderDetail->quantity)
                            ->delete();
                        
                        // Verificar stock del nuevo producto
                        $newProduct = Product::find($validatedData['id_product']);
                        if (!$newProduct) {
                            DB::rollBack();
                            return $this->notFoundResponse('El nuevo producto no existe');
                        }
                        
                        if ($newProduct->current_stock < $validatedData['quantity']) {
                            DB::rollBack();
                            return $this->errorResponse(
                                'Stock insuficiente para el nuevo producto',
                                ['stock_error' => 'Cantidad solicitada excede el stock disponible'],
                                [
                                    'requested_quantity' => $validatedData['quantity'],
                                    'available_stock' => $newProduct->current_stock
                                ]
                            );
                        }
                        
                        // Descontar stock del nuevo producto
                        $newProduct->decrement('current_stock', $validatedData['quantity']);
                        
                        // Registrar nuevo movimiento de stock
                        StockMovement::create([
                            'id_product' => $validatedData['id_product'],
                            'id_order' => $order->id,
                            'id_user_responsible' => Auth::id(),
                            'id_movement_type' => $movementType->id,
                            'quantity_moved' => -$validatedData['quantity'],
                            'movement_date' => now(),
                            'notes' => 'Descuento de stock por actualización de detalle de pedido #' . $order->id
                        ]);
                        
                    } elseif ($quantityChanged) {
                        // Solo cambió la cantidad
                        $product = Product::find($orderDetail->id_product);
                        if (!$product) {
                            DB::rollBack();
                            return $this->notFoundResponse('Producto asociado no encontrado');
                        }
                        
                        $quantityDifference = $validatedData['quantity'] - $orderDetail->quantity;
                        
                        // Verificar si hay stock suficiente para el aumento
                        if ($quantityDifference > 0 && $product->current_stock < $quantityDifference) {
                            DB::rollBack();
                            return $this->errorResponse(
                                'Stock insuficiente para el aumento de cantidad',
                                ['stock_error' => 'No hay suficiente stock disponible'],
                                [
                                    'quantity_increase' => $quantityDifference,
                                    'available_stock' => $product->current_stock
                                ]
                            );
                        }
                        
                        // Actualizar stock
                        $product->decrement('current_stock', $quantityDifference);
                        
                        // Actualizar movimiento de stock existente
                        $stockMovement = StockMovement::where('id_order', $order->id)
                            ->where('id_product', $orderDetail->id_product)
                            ->where('id_movement_type', $movementType->id)
                            ->first();
                        
                        if ($stockMovement) {
                            $stockMovement->update([
                                'quantity_moved' => -$validatedData['quantity'],
                                'notes' => 'Actualización de cantidad en detalle de pedido #' . $order->id
                            ]);
                        } else {
                            // Crear nuevo movimiento si no existe
                            StockMovement::create([
                                'id_product' => $orderDetail->id_product,
                                'id_order' => $order->id,
                                'id_user_responsible' => Auth::id(),
                                'id_movement_type' => $movementType->id,
                                'quantity_moved' => -$validatedData['quantity'],
                                'movement_date' => now(),
                                'notes' => 'Actualización de cantidad en detalle de pedido #' . $order->id
                            ]);
                        }
                    }
                    
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Error al manejar stock en OrderDetailController@update', [
                        'error' => $e->getMessage(),
                        'order_detail_id' => $orderDetail->id,
                        'order_id' => $order->id
                    ]);
                    
                    return $this->errorResponse(
                        'Error al procesar el movimiento de stock',
                        ['stock_movement_error' => $e->getMessage()],
                        [],
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            }
            
            // Actualizar el detalle del pedido
            $lineSubtotal = $validatedData['quantity'] * $validatedData['unit_price_at_order'];
            
            $orderDetail->update([
                'id_product' => $validatedData['id_product'],
                'quantity' => $validatedData['quantity'],
                'unit_price_at_order' => $validatedData['unit_price_at_order'],
                'line_subtotal' => $lineSubtotal,
                'discount_percentage_by_unit' => $validatedData['discount_percentage_by_unit'] ?? 0,
            ]);
            
            // Actualizar totales del pedido
            $this->updateOrderTotals($order->id);
            
            DB::commit();
            
            return $this->successResponse(
                $orderDetail->load(['order', 'product']),
                'Detalle de pedido actualizado exitosamente'
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error de base de datos en OrderDetailController@update', [
                'error' => $e->getMessage(),
                'order_detail_id' => $orderDetail->id ?? null,
                'request' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error de base de datos al actualizar el detalle del pedido',
                ['database_error' => 'Error en la operación de base de datos'],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado en OrderDetailController@update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_detail_id' => $orderDetail->id ?? null,
                'request' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error inesperado al actualizar el detalle del pedido',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderDetail $orderDetail)
    {
        DB::beginTransaction();
        
        try {
            // Verificar que el pedido está en estado 'Pendiente'
            $order = $orderDetail->order;
            if (!$order) {
                DB::rollBack();
                return $this->notFoundResponse('Pedido asociado no encontrado');
            }
            
            if ($order->order_status !== 'Pendiente') {
                DB::rollBack();
                return $this->errorResponse(
                    'Solo se pueden eliminar detalles de pedidos en estado Pendiente',
                    ['order_status' => 'Estado de pedido inválido'],
                    ['current_status' => $order->order_status]
                );
            }
            
            // Manejar reversión de stock para ventas salientes
            if ($order->order_type === 'Venta') {
                try {
                    
                    // Revertir stock
                    $product = Product::find($orderDetail->id_product);
                    if ($product) {
                        $product->increment('current_stock', $orderDetail->quantity);
                    }
                    
                    // Obtener tipo de movimiento
                    $movementType = MovementType::where('name', 'Venta')->first();
                    if ($movementType) {
                        // Eliminar movimiento de stock
                        StockMovement::where('id_order', $order->id)
                            ->where('id_product', $orderDetail->id_product)
                            ->where('id_movement_type', $movementType->id)
                            ->where('quantity_moved', -$orderDetail->quantity)
                            ->delete();
                    }
                    
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Error al revertir stock en OrderDetailController@destroy', [
                        'error' => $e->getMessage(),
                        'order_detail_id' => $orderDetail->id,
                        'product_id' => $orderDetail->id_product
                    ]);
                    
                    return $this->errorResponse(
                        'Error al revertir el stock del producto',
                        ['stock_reversion_error' => $e->getMessage()],
                        [],
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            }
            
            $orderDetailId = $orderDetail->id;
            
            // Eliminar el detalle del pedido
            $orderDetail->delete();
            
            // Actualizar totales del pedido
            $this->updateOrderTotals($order->id);
            
            DB::commit();
            
            return $this->deletedResponse(
                $orderDetailId,
                'Detalle de pedido eliminado exitosamente',
                false // Hard delete
            );
            
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error de base de datos en OrderDetailController@destroy', [
                'error' => $e->getMessage(),
                'order_detail_id' => $orderDetail->id ?? null
            ]);
            
            return $this->errorResponse(
                'Error de base de datos al eliminar el detalle del pedido',
                ['database_error' => 'Error en la operación de base de datos'],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error inesperado en OrderDetailController@destroy', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_detail_id' => $orderDetail->id ?? null
            ]);
            
            return $this->errorResponse(
                'Error inesperado al eliminar el detalle del pedido',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    
    /**
     * Actualizar los totales del pedido
     */
    private function updateOrderTotals($orderId)
    {
        try {
            $order = Order::find($orderId);
            if (!$order) {
                throw new Exception("Pedido con ID {$orderId} no encontrado");
            }
            
            $totalGross = $order->orderDetails()->sum('line_subtotal');
            $totalNet = $totalGross;
            
            $order->update(['total_net' => $totalNet]);
            
        } catch (Exception $e) {
            Log::error('Error al actualizar totales del pedido', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
            
            // Re-lanzar la excepción para que sea manejada por el método principal
            throw $e;
        }
    }
}