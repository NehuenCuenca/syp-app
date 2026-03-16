<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterOrdersRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Contact;
use App\Models\MovementType;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(FilterOrdersRequest $request)
    {
        try {
            $filters = $request->getFilters();
            $query = Order::with(['contact']);
            
            // Filtros
            if (!empty($filters['movement_type_id'])) {
                $query->where('movement_type_id', $request->movement_type_id);
            }

            if (!empty($filters['contact_id'])) {
                $query->where('contact_id', $request->contact_id);
            }

            if (!empty($filters['before_equal_date'])) {
                $query->whereDate('created_at', '<=', $request->before_equal_date);
            }

            $search = $request->get('search', '');
            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "{$search}%")
                    ->orWhereRelation('contact', 'name', 'like', "%{$search}%");
                });
            }
            
            // Ordenamiento
            if (in_array($filters['sort_by'], array_keys(FilterOrdersRequest::ALLOWED_SORT_FIELDS))) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }

            // Paginación
            $orders = $query->paginate($filters['per_page']);

            Log::info('Retrieved orders filtered', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);

            return $this->paginatedResponse(
                $orders,
                'Pedidos filtrados recuperados exitosamente',
                ['filters_applied' => $filters]
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
                'Error inesperado al filtrar los pedidos',
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
            $contacts = Contact::select('id', 'code', 'name', 'deleted_at', 'contact_type')
                ->orderBy('name')
                ->get()
                ->makeHidden(['last_order', 'phone_number_info']);

            $products = Product::with('category:id,name')
                ->select('id', 'code', 'name', 'current_stock', 'min_stock_alert', 'buy_price', 'sale_price', 'category_id', 'deleted_at', 'profit_percentage')
                ->orderBy('name')
                ->get();

            $data = [
                'order_types' => [
                    MovementType::firstWhere('name', MovementType::MOVEMENT_TYPE_BUY),
                    MovementType::firstWhere('name', MovementType::MOVEMENT_TYPE_SALE)
                ],
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
                'Error inesperado del servidor al obtener los datos necesarios',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $orderService = app(OrderService::class);
            $order = $orderService->createOrder($request->validated());

            Log::info('Order created', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
            ]);

            return $this->createdResponse(
                $order,
                'Pedido creado exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error creating order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return $this->errorResponse(
                'Error inesperado al crear el pedido.',
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
    public function show(Request $request, Order $order)
    {
        try {
            $orderData = $order->load(['contact', 'orderDetails.product.category', 'movementType']);

            Log::info('Order has been retrieved', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
            ]);
            
            return $this->successResponse(
                $orderData,
                'Pedido obtenido exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to retrieve a order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
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
     * 
     * Este método revierte temporalmente el stock de los productos del pedido
     * para permitir que el usuario edite el pedido como si nunca se hubiera realizado.
     * 
     * Lógica de reversión:
     * - Si el pedido es una VENTA: el stock aumenta (se devuelven las unidades)
     * - Si el pedido es una COMPRA: el stock disminuye (se quitan las unidades)
     * 
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, Order $order)
    {
        try {
            // Cargar relaciones necesarias
            $order->load([
                'contact',
                'orderDetails.product.category',
                'movementType'
            ]);

            // Obtener todos los contactos
            $contacts = Contact::select('id', 'code', 'name', 'deleted_at', 'contact_type')
                ->orderBy('name')
                ->get()
                ->makeHidden(['last_order', 'phone_number_info']);

            // Obtener todos los productos
            $products = Product::with('category:id,name')
                ->select('id', 'code', 'name', 'current_stock', 'min_stock_alert', 'profit_percentage', 'sale_price', 'buy_price', 'category_id', 'deleted_at')
                ->orderBy('name')
                ->get();

            // Crear un mapa de productos para acceso rápido
            $productsMap = $products->keyBy('id');

            // Revertir el stock temporalmente para cada producto del pedido
            foreach ($order->orderDetails as $detail) {
                if (isset($productsMap[$detail->product_id])) {
                    $product = $productsMap[$detail->product_id];
                    
                    if ($order->is_sale) {
                        // VENTA: Revertir significa AUMENTAR el stock
                        // (porque cuando se vendió, se disminuyó el stock)
                        $product->current_stock += $detail->quantity;
                    } elseif ($order->is_purchase) {
                        // COMPRA: Revertir significa DISMINUIR el stock
                        // (porque cuando se compró, se aumentó el stock)
                        $product->current_stock -= $detail->quantity;
                    }

                    // Asegurar que el stock nunca sea negativo
                    if ($product->current_stock < 0) {
                        $product->current_stock = 0;
                    }
                }
            }

            // Preparar los tipos de pedido disponibles
            $orderTypes = [
                MovementType::firstWhere('name', MovementType::MOVEMENT_TYPE_BUY),
                MovementType::firstWhere('name', MovementType::MOVEMENT_TYPE_SALE)
            ];

            // Preparar la respuesta
            $data = [
                'order' => $order,
                'order_types' => $orderTypes,
                'contacts' => $contacts,
                'products' => $products->values() // Valores actualizados con stock revertido
            ];

            Log::info('Retrieved order data for editing with reverted stock', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
                'order_type' => $order->movementType->name,
                'products_affected' => $order->orderDetails->count()
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
                'file' => $e->getFile(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ]);
            
            return $this->errorResponse(
                'Error inesperado al obtener los datos de edición',
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
        try {
            $orderService = app(OrderService::class);
            $order = $orderService->updateOrder($order, $request->validated());

            Log::info('Order updated', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
            ]);

            return $this->successResponse(
                $order,
                'Pedido actualizado exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error updating order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return $this->errorResponse(
                'Error inesperado al actualizar el pedido.',
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
    public function destroy(Request $request, Order $order)
    {
        try {
            $orderService = app(OrderService::class);
            $orderService->deleteOrder($order);

            Log::info('Order deleted', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
            ]);

            return $this->deletedResponse(
                $order->id,
                'Pedido eliminado exitosamente',
                false
            );
        } catch (Exception $e) {
            Log::error('Error deleting order', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse(
                'Error inesperado al eliminar el pedido.',
                ['exception' => $e->getMessage()],
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
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
            $contacts = Contact::select('id', 'code', 'name', 'deleted_at', 'contact_type')
                                    ->get()
                                    ->makeHidden(['last_order', 'phone_number_info']);
                                    
            $data = [
                'order_types' => [
                    MovementType::firstWhere('name', MovementType::MOVEMENT_TYPE_BUY),
                    MovementType::firstWhere('name', MovementType::MOVEMENT_TYPE_SALE)
                ],
                'contacts' => $contacts,
                'before_equal_date' => Carbon::parse(Order::max('created_at'))->format('Y-m-d'),
                'sort_by' => FilterOrdersRequest::ALLOWED_SORT_FIELDS,
                'sort_direction' => FilterOrdersRequest::ALLOWED_SORT_DIRECTIONS
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
     * Get order details
     */
    public function getOrderDetails(Request $request, Order $order)
    {
        try {
            $order->load(['orderDetails.product.category']);

            Log::info('Order and his details has been retrieved', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
            ]);

            return $this->successResponse(
                $order->orderDetails,
                'Detalles del pedido obtenidos exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to show order and his details', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
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
                'order_id' => $order->id,
            ]);
            
            return $this->successResponse(
                $order->stockMovements,
                'Movimientos de stock obtenidos exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to show order and his details', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'order_id' => $order->id,
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
}