<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Http\Requests\StoreOrderDetailRequest;
use App\Http\Requests\UpdateOrderDetailRequest;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
            
            if ($request->has('order_id')) {
                // Validar que el pedido existe
                $specifiedOrder = Order::find($request->order_id);
                if (!$specifiedOrder) {
                    return $this->notFoundResponse('El pedido especificado no existe');
                }
                
                $query = OrderDetail::where('order_id', $request->order_id)
                    ->with(['product.category'])
                    ->get();
            } else {
                $query = OrderDetail::with(['product.category'])->get();
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
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
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
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $products = Product::with('category:id,name')
                ->select('id', 'code', 'name', 'current_stock', 'min_stock_alert', 'buy_price', 'sale_price', 'profit_percentage', 'category_id', 'deleted_at')
                ->orderBy('name')
                ->get();

            $data = [
                'products' => $products
            ];

            return $this->successResponse(
                $data,
                'Datos para crear detalle de pedido obtenidos exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener datos para crear detalle de pedido: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los datos necesarios para crear un detalle de pedido',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
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
            $orderId = $validatedData['order_id'];
            
            // Verificar que el pedido existe 
            $order = Order::find($orderId);
            if (!$order) {
                DB::rollBack();
                return $this->notFoundResponse('El pedido especificado no existe');
            }
            
            // Verificar que el producto existe
            $product = Product::find($orderData['product_id']);
            if (!$product) {
                DB::rollBack();
                return $this->notFoundResponse('El producto especificado no existe');
            }
            
            // Verificar disponibilidad de stock para ventas salientes
            if ($order->getIsSaleAttribute()) {
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
            $lineSubtotal = $orderData['quantity'] * $orderData['unit_price'];
            
            // Crear el detalle del pedido
            $orderDetail = OrderDetail::create([
                'order_id' => $orderId,
                'product_id' => $orderData['product_id'],
                'quantity' => $orderData['quantity'],
                'unit_price' => $orderData['unit_price'],
                'line_subtotal' => $lineSubtotal,
                'percentage_applied' => $orderData['percentage_applied'] ?? 0,
            ]);
            
            // Manejar stock y movimientos 
            try {
                if($order->getIsSaleAttribute()){
                    $product->decrement('current_stock', $orderData['quantity']);
                } else {
                    $product->increment('current_stock', $orderData['quantity']);
                    $product->update(['buy_price' => $orderData['unit_price'], 'profit_percentage' => $orderData['profit_percentage']]);
                }
                
                $quantityMoved = $order->getIsSaleAttribute() ? -$orderData['quantity'] : $orderData['quantity'];
                // Registrar movimiento de stock
                StockMovement::create([
                    'product_id' => $orderData['product_id'],
                    'order_id' => $orderId,
                    'order_detail_id' => $orderDetail->id,
                    'movement_type_id' => $order->movement_type_id,
                    'quantity_moved' => $quantityMoved,
                    'notes' => "Detalle de pedido '{$order->code}' agregado"
                ]);
                
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error al manejar stock en OrderDetailController@store', [
                    'error' => $e->getMessage(),
                    'product_id' => $orderData['product_id'],
                    'order_id' => $orderId
                ]);
                
                return $this->errorResponse(
                    'Error al procesar el movimiento de stock',
                    ['exception' => $e->getMessage()],
                    [],
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
                );
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
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
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
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderDetail $orderDetail)
    {
        try {
            $orderDetail->load(['order', 'product.category']);
            
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
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
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
                'product_id' => $request->validated('product_id', $orderDetail->product_id),
                'quantity' => abs($request->validated('quantity', $orderDetail->quantity)),
                'unit_price' => $request->validated('unit_price', $orderDetail->unit_price),
                'percentage_applied' => $request->validated('percentage_applied', $orderDetail->percentage_applied),
                'profit_percentage' => $request->validated('profit_percentage', $orderDetail->product->profit_percentage),
            ];
        
            $order = $orderDetail->order;
            if (!$order) {
                DB::rollBack();
                return $this->notFoundResponse('Pedido asociado no encontrado');
            }
            
            // Verificar que el nuevo producto existe (si cambió)
            if (isset($validatedData['product_id']) && $validatedData['product_id'] != $orderDetail->product_id) {
                $newProduct = Product::find($validatedData['product_id']);
                if (!$newProduct) {
                    DB::rollBack();
                    return $this->notFoundResponse('El nuevo producto especificado no existe');
                }
            }
            
            // Verificar si hay cambios en cantidad o precio
            $quantityChanged = $orderDetail->quantity != $validatedData['quantity'];
            $unitPriceChanged = $orderDetail->unit_price != $validatedData['unit_price'];
            $discountChanged = ($orderDetail->percentage_applied ?? 0) != ($validatedData['percentage_applied'] ?? 0);
            $profitChanged = ($orderDetail->product->profit_percentage ?? 0) != ($validatedData['profit_percentage'] ?? 0);

            if (!$quantityChanged && !$unitPriceChanged && !$discountChanged && !$profitChanged) {
                DB::rollBack();
                return $this->errorResponse(
                    'No se detectaron cambios en el detalle del pedido',
                    ['no_changes' => 'Los datos enviados son idénticos a los actuales']
                );
            }
            
            try {
                if ($quantityChanged) {
                    // Solo cambió la cantidad
                    $product = Product::find($orderDetail->product_id);
                    if (!$product) {
                        DB::rollBack();
                        return $this->notFoundResponse('Producto asociado no encontrado');
                    }
                    
                    $quantityDifference = $validatedData['quantity'] - $orderDetail->quantity;
                    
                    // Verificar si hay stock suficiente para el aumento
                    if ($quantityDifference > $product->current_stock) {
                        DB::rollBack();
                        return $this->errorResponse(
                            'Stock insuficiente para el aumento de cantidad',
                            ['stock_error' => 'No hay suficiente stock disponible'],
                            [
                                'quantity_difference' => $quantityDifference,
                                'available_stock' => $product->current_stock
                            ]
                        );
                    }

                    // Actualizar stock
                    if($order->getIsSaleAttribute()){
                        $product->decrement('current_stock', $quantityDifference); 
                    } else {
                        $product->increment('current_stock', $quantityDifference);
                    }

                    
                    // Actualizar movimiento de stock existente
                    if ($orderDetail->stockMovement) {
                        $orderDetail->stockMovement->update([
                            'quantity_moved' => ($order->getIsSaleAttribute()) ? -$validatedData['quantity'] : $validatedData['quantity'],
                            'notes' => "Actualización de cantidad en detalle de pedido '{$order->code}'"
                        ]);
                    } else {
                        //Crear nuevo movimiento si no existe
                        StockMovement::create([
                            'product_id' => $orderDetail->product_id,
                            'order_id' => $order->id,
                            'order_detail_id' => $orderDetail->id,
                            'movement_type_id' => $order->movement_type_id,
                            'quantity_moved' => ($order->getIsSaleAttribute()) ? -$validatedData['quantity'] : $validatedData['quantity'],
                            'notes' => "Actualización de cantidad en detalle de pedido '{$order->code}'"
                        ]);
                    }
                    
                    $orderDetail->update([ 'quantity' => ($validatedData['quantity'] ?? $orderDetail->quantity) ]);
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
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
                );
            }
            
            // Actualizar el detalle del pedido
            $lineSubtotal = $validatedData['quantity'] * $validatedData['unit_price'];
            
            $orderDetail->update([
                'unit_price' => $validatedData['unit_price'],
                'line_subtotal' => $lineSubtotal,
                'percentage_applied' => $validatedData['percentage_applied'] ?? 0,
            ]);
            
            // Actualizar totales del pedido
            $this->updateOrderTotals($order->id);

            //Actualizar precio de producto
            if($order->getIsPurchaseAttribute() && $request->filled('unit_price')){
                $orderDetail->product->update(['buy_price' => $validatedData['unit_price'], 'profit_percentage' => $validatedData['profit_percentage']]);
            }
            
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
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
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
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
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
            $order = $orderDetail->order;
            if (!$order) {
                DB::rollBack();
                return $this->notFoundResponse('Pedido asociado no encontrado');
            }
            
            // Manejar reversión de stock 
            try {
                
                // Revertir stock
                $product = Product::find($orderDetail->product_id);
                if ($order->getIsSaleAttribute()) {
                    $product->increment('current_stock', $orderDetail->quantity);
                } else {
                    $product->decrement('current_stock', $orderDetail->quantity);
                }

                // Eliminar movimientos de stock
                $orderDetail->stockMovement()->delete();
                
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error al revertir stock en OrderDetailController@destroy', [
                    'error' => $e->getMessage(),
                    'order_detail_id' => $orderDetail->id,
                    'product_id' => $orderDetail->product_id
                ]);
                
                return $this->errorResponse(
                    'Error al revertir el stock del producto',
                    ['stock_reversion_error' => $e->getMessage()],
                    [],
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
                );
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
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
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
                Response::HTTP_INTERNAL_SERVER_ERROR,
                config('app.debug') ? $e : null
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
            
            $subTotal = $order->orderDetails()->sum('line_subtotal');
            $totalNet = $subTotal + $order->adjustment_amount;
            
            $order->update(['subtotal' => $subTotal, 'total_net' => $totalNet]);
            
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