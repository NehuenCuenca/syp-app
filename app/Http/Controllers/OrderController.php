<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Contact;
use App\Models\MovementType;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\StockMovement;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $orders = Order::with(['contact', 'userCreator'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return $this->successResponse(
                $orders, 
                'Todos los pedidos recuperados exitosamente',
                ['total' => $orders->count()]
            );

        } catch (Exception $e) {
            Log::error('Error al obtener los pedidos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los pedidos',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $contacts = Contact::select('id', 'company_name', 'contact_name', 'contact_type')
                ->orderBy('company_name')
                ->get();

            $products = Product::with('category:id,name')
                ->select('id', 'sku', 'name', 'current_stock', 'buy_price', 'sale_price', 'id_category')
                ->orderBy('name')
                ->get();

            $data = [
                'order_types' => Order::getOrderTypes(),
                'order_statuses' => Order::getOrderStatuses(),
                'contacts' => $contacts,
                'products' => $products
            ];

            return $this->successResponse(
                $data,
                'Datos para crear pedido obtenidos exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener datos para crear pedido: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los datos necesarios',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Crear el pedido
            $order = Order::create([
                'id_contact' => $request->id_contact,
                'id_user_creator' => $request->id_user_creator,
                'order_type' => $request->order_type,
                'order_status' => $request->order_status ?? 'Pendiente',
                'notes' => $request->notes,
                'total_net' => $request->total_net ?? 0,
            ]);

            if (!$order) {
                throw new Exception('No se pudo crear el pedido');
            }

            $totalDiscount = 0;
            $totalGross = 0;
            $totalNet = 0;

            // Crear los detalles del pedido
            if (!empty($request->order_details)) {
                foreach ($request->order_details as $detail) {
                    $product = Product::find($detail['id_product']);
                    
                    if (!$product) {
                        throw new Exception("Producto con ID {$detail['id_product']} no encontrado");
                    }

                    $lineGrossSubtotal = $detail['quantity'] * $detail['unit_price_at_order'];
                    $totalDiscount += $detail['quantity'] * $detail['unit_price_at_order'] * ($detail['discount_percentage_by_unit'] ?? 0);
                    $totalGross += $lineGrossSubtotal;
                    
                    $stockToDiscount = ($detail['quantity'] >= $product->current_stock && $order->getIsSaleAttribute()) 
                        ? $product->current_stock 
                        : $detail['quantity'];

                    $detailRecord = OrderDetail::create([
                        'id_order' => $order->id,
                        'id_product' => $detail['id_product'],
                        'quantity' => $stockToDiscount,
                        'unit_price_at_order' => $detail['unit_price_at_order'],
                        'discount_percentage_by_unit' => $detail['discount_percentage_by_unit'] ?? 0,
                    ]);

                    if (!$detailRecord) {
                        throw new Exception('Error al crear detalle del pedido');
                    }

                    if ($order->getIsPurchaseAttribute()) {
                        $product->update(['buy_price' => $detail['unit_price_at_order']]);
                    }

                    if ($order->getIsSaleAttribute()) {
                        $this->createStockMovement($order, $detailRecord);
                    }
                }
            }

            $totalNet = (float)($request->filled('total_net') ? $request->integer('total_net') : ($totalGross - $totalDiscount));
            $order->update(['total_net' => $totalNet]);

            DB::commit();

            $order->append(['show_valid_transitions']);
            $orderData = $order->load(['contact', 'userCreator', 'orderDetails.product.category']);
            
            return $this->createdResponse(
                $orderData,
                'Pedido creado exitosamente'
            );

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear pedido: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error al crear el pedido',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        try {
            $orderData = $order->load(['contact', 'userCreator', 'orderDetails.product.category', 'stockMovements']);
            
            return $this->successResponse(
                $orderData,
                'Pedido obtenido exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener pedido: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener el pedido',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        try {
            $order->load(['contact', 'userCreator', 'orderDetails.product.category']);

            $contacts = Contact::select('id', 'company_name', 'contact_name', 'contact_type')
                ->orderBy('company_name')
                ->get();

            $products = Product::with('category:id,name')
                ->select('id', 'sku', 'name', 'current_stock', 'sale_price', 'buy_price', 'id_category')
                ->orderBy('name')
                ->get();

            $data = [
                'order' => $order,
                'contacts' => $contacts,
                'products' => $products,
                'order_types' => Order::getOrderTypes(),
                'order_statuses' => Order::getOrderStatuses(),
            ];

            return $this->successResponse(
                $data,
                'Datos para editar pedido obtenidos exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener datos para editar pedido: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los datos de edición',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        DB::beginTransaction();
        
        try {
            $oldStatus = $order->order_status;
            $newStatus = $request->order_status;

            // Actualizar el pedido
            $updateResult = $order->update([
                'id_contact' => $request->id_contact,
                'actual_delivery_date' => $request->actual_delivery_date,
                'notes' => $request->notes,
                'total_net' => $request->total_net ?? $order->total_net
            ]);

            if (!$updateResult) {
                throw new Exception('No se pudo actualizar el pedido');
            }

            // Manejar cambios de estado y movimientos de stock
            if ($newStatus !== $oldStatus) {
                $order->update(['order_status' => $newStatus]);
                $this->handleStatusChange($order, $oldStatus, $newStatus);
            }

            DB::commit();

            $order->append(['show_valid_transitions']);
            $orderData = $order->load(['contact', 'userCreator', 'orderDetails.product']);
            
            return $this->successResponse(
                $orderData,
                'Pedido actualizado exitosamente'
            );

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar pedido: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error al actualizar el pedido',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        DB::beginTransaction();
        
        try {
            if ($order->orderDetails()->exists()) {
                foreach ($order->orderDetails->all() as $detail) {
                    $quantityToRevert = StockMovement::where('id_order', $order->id)
                        ->where('id_product', $detail->id_product)
                        ->sum('quantity_moved');

                    // Actualizar stock
                    $product = Product::find($detail->id_product);
                    if ($product) {
                        $product->decrement('current_stock', $quantityToRevert);
                    }
                }
            }
           
            $order->stockMovements()->delete();
            $order->orderDetails()->delete();            
            $deleteResult = $order->delete();

            if (!$deleteResult) {
                throw new Exception('No se pudo eliminar el pedido');
            }

            DB::commit();
            
            return $this->deletedResponse(
                $order->id,
                'Pedido eliminado exitosamente'
            );

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar pedido: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error al eliminar el pedido',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Get filters to be used in the index view
     */
    public function getFilters()
    {
        try {
            $data = [
                'order_types' => Order::getOrderTypes(),
                'order_statuses' => Order::getOrderStatuses(),
                'contacts' => Contact::select('id', 'company_name', 'contact_name', 'contact_type')->get(),
                'date_from' => Order::min('created_at'),
                'date_to' => Order::max('created_at'),
                'sort_by' => self::ALLOWED_SORT_FIELDS,
                'sort_direction' => self::ALLOWED_SORT_DIRECTIONS
            ];

            return $this->successResponse(
                $data,
                'Datos para filtrar pedidos obtenidos exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener filtros de pedidos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los filtros',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Get filtered and ordered orders
     */
    public function getFilteredOrders(Request $request)
    {
        try {
            $query = Order::with(['contact', 'userCreator']);
            
            // Filtros
            if ($request->filled('order_type')) {
                $query->where('order_type', $request->order_type);
            }

            if ($request->filled('order_status')) {
                $query->where('order_status', $request->order_status);
            }

            if ($request->filled('id_contact')) {
                $query->where('id_contact', $request->id_contact);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $search = $request->get('search', '');
            if ($request->has('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            }
            
            // Ordenamiento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');

            if (in_array($sortBy, array_keys(self::ALLOWED_SORT_FIELDS))) {
                $query->orderBy($sortBy, $sortDirection);
            }

            // Paginación
            $perPage = $request->get('per_page', 9);
            $orders = $query->paginate($perPage);

            $meta = [
                'filters_applied' => [
                    'search' => $search,
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                    'per_page' => $perPage,
                    'page' => $request->integer('page', 1)
                ]
            ];

            return $this->paginatedResponse(
                $orders,
                'Pedidos filtrados recuperados exitosamente',
                $meta
            );

        } catch (Exception $e) {
            Log::error('Error al filtrar pedidos: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al filtrar los pedidos',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails(Order $order)
    {
        try {
            $order->load(['orderDetails.product.category']);
            return $this->successResponse(
                $order->orderDetails,
                'Detalles del pedido obtenidos exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener detalles del pedido: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los detalles',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    /**
     * Get stock movements for order
     */
    public function getStockMovements(Order $order)
    {
        try {
            $order->load(['stockMovements.product.category']);
            
            return $this->successResponse(
                $order->stockMovements,
                'Movimientos de stock obtenidos exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener movimientos de stock del pedido: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los movimientos de stock',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    }

    // Constantes para filtros
    public const ALLOWED_SORT_FIELDS = [
        'id' => 'ID', 
        'created_at' => 'Fecha de creacion', 
        'actual_delivery_date' => 'Fecha de entrega',
        'order_type' => 'Tipo de pedido',
        'order_status' => 'Estado de pedido',
        'total_net' => 'Total neto'
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'asc' => 'Ascendente',
        'desc' => 'Descendente'
    ];

    /**
     * Manejar cambios de estado y movimientos de stock
     */
    private function handleStatusChange(Order $order, string $oldStatus, string $newStatus)
    {
        try {
            $fromPendingToCompleted  = $oldStatus === 'Pendiente' && $newStatus === 'Completado';
            $fromPendingToCanceled   = $oldStatus === 'Pendiente' && $newStatus === 'Cancelado';
            $fromCompletedToPending  = $oldStatus === 'Completado' && $newStatus === 'Pendiente';
            $fromCompletedToReturned = $oldStatus === 'Completado' && $newStatus === 'Devuelto';
            $fromCanceledToPending   = $oldStatus === 'Cancelado' && $newStatus === 'Pendiente';
            $fromReturnedToPending   = $oldStatus === 'Devuelto' && $newStatus === 'Pendiente';
            $fromReturnedToCompleted = $oldStatus === 'Devuelto' && $newStatus === 'Completado';

            if ($order->getIsSaleAttribute()) {
                if ($fromPendingToCanceled) {
                    $this->revertStockMovements($order);
                } elseif ($fromCompletedToReturned) {
                    $this->createReturnMovements($order);
                } elseif ($fromCanceledToPending || $fromReturnedToPending || $fromReturnedToCompleted) {
                    foreach ($order->orderDetails as $detail) {
                        $this->createStockMovement($order, $detail);
                    }
                }
            }  
            
            if ($order->getIsPurchaseAttribute()) {
                if ($fromPendingToCompleted || $fromReturnedToCompleted) {
                    foreach ($order->orderDetails as $detail) {
                        $this->createStockMovement($order, $detail);
                    }
                } elseif ($fromCompletedToPending) {
                    $this->revertStockMovements($order);
                } elseif ($fromCompletedToReturned) {
                    $this->createReturnMovements($order);
                }
            }

        } catch (Exception $e) {
            Log::error('Error al manejar cambio de estado: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new Exception('Error al procesar el cambio de estado del pedido: ' . $e->getMessage());
        }
    }

    /**
     * Crear movimiento de stock para un detalle del pedido
     */
    private function createStockMovement(Order $order, OrderDetail $detail)
    {
        try {
            $movementType = $order->getIsPurchaseAttribute() ? 'Compra' : 'Venta';
            $quantity = ($order->getIsPurchaseAttribute()) ? $detail->quantity : -$detail->quantity;

            $movementTypeRecord = MovementType::where('name', $movementType)->first();
            if (!$movementTypeRecord) {
                throw new Exception("Tipo de movimiento '{$movementType}' no encontrado");
            }

            $stockMovement = StockMovement::create([
                'id_product' => $detail->id_product,
                'id_order' => $order->id,
                'id_user_responsible' => $order->id_user_creator,
                'id_movement_type' => $movementTypeRecord->id,
                'quantity_moved' => $quantity,
                'movement_date' => now(),
                'notes' => "Movimiento automático por pedido #{$order->id}",
            ]);

            if (!$stockMovement) {
                throw new Exception('No se pudo crear el movimiento de stock');
            }

            // Actualizar stock actual del producto
            $product = Product::find($detail->id_product);
            if (!$product) {
                throw new Exception("Producto con ID {$detail->id_product} no encontrado");
            }
            
            $product->increment('current_stock', $quantity);

        } catch (Exception $e) {
            Log::error('Error al crear movimiento de stock: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'detail_id' => $detail->id,
                'product_id' => $detail->id_product,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new Exception('Error al crear movimiento de stock: ' . $e->getMessage());
        }
    }

    /**
     * Revertir movimientos de stock de un pedido
     */
    private function revertStockMovements(Order $order)
    {
        try {
            $orderDetails = OrderDetail::where('id_order', $order->id)->get();
            
            foreach ($orderDetails as $detail) {
                $movementType = $order->getIsPurchaseAttribute() ? 'Ajuste Negativo' : 'Ajuste Positivo';
                $quantity = $order->order_type === 'Compra' ? -$detail['quantity'] : $detail['quantity'];

                $movementTypeRecord = MovementType::where('name', $movementType)->first();
                if (!$movementTypeRecord) {
                    throw new Exception("Tipo de movimiento '{$movementType}' no encontrado");
                }

                // Crear movimiento inverso
                $stockMovement = StockMovement::create([
                    'id_product' => $detail->id_product,
                    'id_order' => $order->id,
                    'id_user_responsible' => auth()->id() ?? $order->id_user_creator,
                    'id_movement_type' => $movementTypeRecord->id,
                    'quantity_moved' => $quantity,
                    'movement_date' => now(),
                    'notes' => "Reversión de movimiento por cambio de estado del pedido #{$order->id}",
                ]);

                if (!$stockMovement) {
                    throw new Exception('No se pudo crear el movimiento de reversión');
                }

                // Actualizar stock
                $product = Product::find($detail->id_product);
                if (!$product) {
                    throw new Exception("Producto con ID {$detail->id_product} no encontrado");
                }
                
                $product->decrement('current_stock', $quantity);
            }

        } catch (Exception $e) {
            Log::error('Error al revertir movimientos de stock: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new Exception('Error al revertir movimientos de stock: ' . $e->getMessage());
        }
    }

    /**
     * Crear movimientos de devolución
     */
    private function createReturnMovements(Order $order)
    {
        try {
            $returnType = $order->order_type === 'Compra' ? 'Devolucion Proveedor' : 'Devolucion Cliente';
            
            $movementTypeRecord = MovementType::where('name', $returnType)->first();
            if (!$movementTypeRecord) {
                throw new Exception("Tipo de movimiento '{$returnType}' no encontrado");
            }
            
            foreach ($order->orderDetails as $detail) {
                $quantity = $order->order_type === 'Compra' ? -$detail->quantity : $detail->quantity;
                
                $stockMovement = StockMovement::create([
                    'id_product' => $detail->id_product,
                    'id_order' => $order->id,
                    'id_user_responsible' => auth()->id() ?? $order->id_user_creator,
                    'id_movement_type' => $movementTypeRecord->id,
                    'quantity_moved' => $quantity,
                    'movement_date' => now(),
                    'notes' => "Devolución del pedido #{$order->id}",
                ]);

                if (!$stockMovement) {
                    throw new Exception('No se pudo crear el movimiento de devolución');
                }

                // Actualizar stock
                $product = Product::find($detail->id_product);
                if (!$product) {
                    throw new Exception("Producto con ID {$detail->id_product} no encontrado");
                }
                
                $product->increment('current_stock', $quantity);
            }

        } catch (Exception $e) {
            Log::error('Error al crear movimientos de devolución: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new Exception('Error al crear movimientos de devolución: ' . $e->getMessage());
        }
    }
}