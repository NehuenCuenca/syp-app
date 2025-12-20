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
use Carbon\Carbon;
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
    public function index(Request $request)
    {
        try {
            $orders = Order::with(['contact', 'movementType'])
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('All orders retrieved (without filters)', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);    

            return $this->successResponse(
                $orders, 
                'Todos los pedidos recuperados exitosamente',
                ['total' => $orders->count()]
            );

        } catch (Exception $e) {
            Log::error('Error trying to get all orders (without filters)', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los pedidos',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $contacts = Contact::select('id', 'code', 'company_name', 'contact_name', 'deleted_at', 'contact_type')
                ->orderBy('company_name')
                ->get();

            $products = Product::with('category:id,name')
                ->select('id', 'code', 'name', 'current_stock', 'min_stock_alert', 'buy_price', 'sale_price', 'id_category', 'deleted_at', 'profit_percentage')
                ->orderBy('name')
                ->get();

            $data = [
                'order_types' => Order::getOrderTypes(),
                'contacts' => $contacts,
                'products' => $products
            ];

            Log::info('Retrieve the data necessary to create an order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip()
            ]);

            return $this->successResponse(
                $data,
                'Datos para crear pedido obtenidos exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to get the data for create an order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los datos necesarios',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Crear el pedido
            $order = Order::create([
                'id_contact' => $request->id_contact,
                'id_movement_type' => $request->id_movement_type,
                'notes' => $request->notes,
                'adjustment_amount' => $request->adjustment_amount ?? 0,
            ]);

            if (!$order) {
                throw new Exception('No se pudo crear el pedido');
            }

            // Crear los detalles del pedido
            $this->createDetails($request->order_details, $order);

            $orderCode = substr(MovementType::find($request->id_movement_type)->name, 0, 1) . $order->id;
            $detailsSubtotal = $order->orderDetails->sum('line_subtotal');
            $totalNet = ($detailsSubtotal + $request->adjustment_amount);            

            $order->update([
                'code' => $orderCode,
                'subtotal' => $detailsSubtotal,
                'total_net' => $totalNet
            ]);

            DB::commit();

            $orderData = $order->load(['contact', 'orderDetails.product.category', 'movementType']);

            Log::info('Retrieve the data necessary to create an order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id
            ]);
            
            return $this->createdResponse(
                $orderData,
                'Pedido creado exitosamente'
            );
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error trying to create an order: ', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error al crear el pedido',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    public function createDetails(array|null $details, Order $order)
    {
        if (!empty($details) && $details!==null) {
            foreach ($details as $detail) {
                $product = Product::find($detail['id_product']);
                
                if (!$product) { throw new Exception("Producto con ID {$detail['id_product']} no encontrado"); }
                
                $stockToDiscount = ($order->getIsSaleAttribute() && $detail['quantity'] >= $product->current_stock) 
                    ? $product->current_stock //Si la cantidad a vender es mayor al stock disponible, se descuenta lo max de stock (el stock queda en 0)
                    : $detail['quantity'];

                $detailRecord = OrderDetail::create([
                    'id_order' => $order->id,
                    'id_product' => $detail['id_product'],
                    'quantity' => $stockToDiscount,
                    'unit_price' => $detail['unit_price'],
                    'percentage_applied' => $detail['percentage_applied'] ?? 0,
                ]);

                if (!$detailRecord) { throw new Exception('Error al crear detalle del pedido'); }
                Log::info('Detail of order has been created', [
                    'id_detail' => $detailRecord->id,
                    'id_product' => $detail['id_product'],
                ]);

                if ($order->getIsPurchaseAttribute()) {
                    $product->update(['buy_price' => $detail['unit_price'], 'profit_percentage' => $detail['percentage_applied']]);

                    Log::info('Update buy price and profit percentage of product', [
                        'id_product' => $product->id,
                        'buy_price' => $detail['unit_price'],
                        'profit_percentage' => $detail['percentage_applied'],
                    ]);
                }
                
                $this->createStockMovement($order, $detailRecord);
            }
        }else {
            throw new Exception('No se proporcionaron detalles al pedido');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Order $order)
    {
        try {
            $orderData = $order->load(['contact', 'orderDetails.product.category', 'movementType']);

            Log::info('Order has been retrieved', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
            ]);
            
            return $this->successResponse(
                $orderData,
                'Pedido obtenido exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to retrieve a order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener el pedido',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Order $order)
    {
        try {
            $order->load(['contact', 'orderDetails.product.category']);

            $contacts = Contact::select('id', 'code', 'company_name', 'contact_name', 'deleted_at', 'contact_type')
                ->orderBy('company_name')
                ->get();

            $products = Product::with('category:id,name')
                ->select('id', 'code', 'name', 'current_stock', 'min_stock_alert', 'profit_percentage', 'sale_price', 'buy_price', 'id_category', 'deleted_at')
                ->orderBy('name')
                ->get();

            foreach($order->orderDetails as $detail) {
                // Modificación temporal del stock
                $quantityToRevert = ($order->getIsSaleAttribute()) ? $detail->quantity : -$detail->quantity;
                $detail->product->current_stock = $detail->product->current_stock + $quantityToRevert;
            }

            $data = [
                'order' => $order,
                'contacts' => $contacts,
                'products' => $products,
                'order_types' => Order::getOrderTypes(),
            ];

            Log::info('Retrieved data to edit an order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
            ]);

            return $this->successResponse(
                $data,
                'Datos para editar pedido obtenidos exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to retrieve the data to edit an order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los datos de edición',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
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
            // Actualizar el pedido
            $updatedOrder = $order->update([
                'id_contact' => $request->id_contact,
                'notes' => $request->notes,
                'adjustment_amount' => $request->adjustment_amount,
            ]);

            if (!$updatedOrder) {
                throw new Exception('No se pudo actualizar el pedido');
            }

            if($request->has('order_details')){
                // revert product stock of each detail
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

                $order->stockMovements()->delete();
                $order->orderDetails()->delete();

                $this->createDetails($request->order_details, $order);

                $detailsSubtotal = $order->refresh()->orderDetails->sum('line_subtotal');
                $totalNet = ($detailsSubtotal + $request->adjustment_amount);            

                $order->update([
                    'subtotal' => $detailsSubtotal,
                    'total_net' => $totalNet
                ]);
            }

            DB::commit();

            Log::info('Order has been updated', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
            ]);

            $orderData = $order->load(['contact', 'orderDetails.product.category', 'movementType']);
            
            return $this->successResponse(
                $orderData,
                'Pedido actualizado exitosamente'
            );

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error trying updating a order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error al actualizar el pedido',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Order $order)
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

            Log::info('Order, details and stock movements has been deleted', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
            ]);
            
            return $this->deletedResponse(
                $order->id,
                'Pedido eliminado exitosamente',
                false
            );
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error trying to delete an order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error al eliminar el pedido',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Get filters to be used in the index view
     */
    public function getFilters(Request $request)
    {
        try {
            $data = [
                'order_types' => Order::getOrderTypes(),
                'contacts' => Contact::select('id', 'code', 'company_name', 'contact_name', 'deleted_at', 'contact_type')->get(),
                'before_equal_date' => Carbon::parse(Order::min('created_at'))->format('Y-m-d'),
                'sort_by' => self::ALLOWED_SORT_FIELDS,
                'sort_direction' => self::ALLOWED_SORT_DIRECTIONS
            ];

            Log::info('Retrieved the filters for orders', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse(
                $data,
                'Datos para filtrar pedidos obtenidos exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Unexpected error trying to get the order filters', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los filtros',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Get filtered and ordered orders
     */
    public function getFilteredOrders(Request $request)
    {
        try {
            $query = Order::with(['contact']);
            
            // Filtros
            if ($request->filled('id_movement_type')) {
                $query->where('id_movement_type', $request->id_movement_type);
            }

            if ($request->filled('id_contact')) {
                $query->where('id_contact', $request->id_contact);
            }

            if ($request->filled('before_equal_date')) {
                $query->whereDate('created_at', '<=', $request->before_equal_date);
            }

            $search = $request->get('search', '');
            if ($request->has('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "{$search}%")
                    ->orWhereRelation('contact', 'company_name', 'like', "%{$search}%");
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

            Log::info('Retrieved orders filtered', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);

            return $this->paginatedResponse(
                $orders,
                'Pedidos filtrados recuperados exitosamente',
                $meta
            );
        } catch (Exception $e) {
            Log::error('Error trying to get filtered orders', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al filtrar los pedidos',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails(Request $request, Order $order)
    {
        try {
            $order->load(['orderDetails.product.category']);

            Log::info('Order and his details has been retrieved', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
            ]);

            return $this->successResponse(
                $order->orderDetails,
                'Detalles del pedido obtenidos exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to show order and his details', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los detalles',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Get stock movements for order
     */
    public function getStockMovements(Request $request,Order $order)
    {
        try {
            $order->load(['stockMovements.product.category']);

            Log::info('Order and his stock movements has been retrieved', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
            ]);
            
            return $this->successResponse(
                $order->stockMovements,
                'Movimientos de stock obtenidos exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to show order and his details', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'id_order' => $order->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);
            
            return $this->errorResponse(
                'Error interno del servidor al obtener los movimientos de stock',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    // Constantes para filtros
    public const ALLOWED_SORT_FIELDS = [
        'id' => 'ID', 
        'created_at' => 'Fecha de creacion', 
        'id_movement_type' => 'Tipo de pedido',
        // 'total_net' => 'Total neto'
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'asc' => 'Ascendente',
        'desc' => 'Descendente'
    ];

    /**
     * Crear movimiento de stock para un detalle del pedido
     */
    private function createStockMovement(Order $order, OrderDetail $detail)
    {
        try {
            $quantity = ($order->getIsSaleAttribute()) ? -$detail->quantity : $detail->quantity;
            $lineSubtotalAsCurrency = $detail->formatToCurrency($detail->line_subtotal);
            $movementAction = ($order->getIsSaleAttribute()) 
                                ? "Vendí a $lineSubtotalAsCurrency" 
                                : "Compré a $lineSubtotalAsCurrency";
            if ($detail->quantity > 1) {
                $unitPriceAsCurrency = $detail->formatToCurrency($detail->unit_price);
                $movementAction.= " (x1: {$unitPriceAsCurrency})";
            }

            $stockMovement = StockMovement::create([
                'id_product' => $detail->id_product,
                'id_order' => $order->id,
                'id_order_detail' => $detail->id,
                'id_movement_type' => $order->id_movement_type,
                'quantity_moved' => $quantity,
                'notes' => $movementAction,
            ]);

            if (!$stockMovement) {
                throw new Exception('No se pudo crear el movimiento de stock');
            }
            Log::info('Stock movement has been created');

            // Actualizar stock actual del producto
            $product = Product::find($detail->id_product);
            if (!$product) {
                throw new Exception("Producto con ID {$detail->id_product} no encontrado");
            }
            
            $product->increment('current_stock', $quantity);
            Log::info('Stock of product has been updated');
        } catch (Exception $e) {
            Log::error('Error trying to create the stock movement', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'detail' => $detail->toArray()
            ]);
            
            throw new Exception('Error al crear movimiento de stock: ' . $e->getMessage());
        }
    }
}