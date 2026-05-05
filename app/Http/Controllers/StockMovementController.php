<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterStockMovementsRequest;
use App\Models\StockMovement;
use App\Models\Product;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Contact;
use App\Models\MovementType;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class StockMovementController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(FilterStockMovementsRequest $request): JsonResponse
    {
        try {
            $filters = $request->getFilters();
            $query = StockMovement::with(['product', 'order.contact', 'movementType', 'orderDetail']);

            // Filtros
            if (!empty($filters['order_id'])) {
                $query->where('order_id', $request->order_id);
            }

            if (!empty($filters['product_id'])) {
                $query->where('product_id', $request->product_id);
            }

            if (!empty($filters['movement_type_id'])) {
                $query->where('movement_type_id', $request->movement_type_id);
            }

            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $search = $request->get('search', '');
            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($search) {
                        $q->where('notes', 'like', "%{$search}%")
                            ->orWhereRelation('orders', 'notes', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            if (in_array($filters['sort_by'], array_keys(FilterStockMovementsRequest::ALLOWED_SORT_FIELDS))) {
                $query->orderBy($filters['sort_by'], $filters['sort_direction']);
            }

            // Paginación
            $stockMovements = $query->paginate($filters['per_page']);

            Log::info('Retrieve filtered stock movements', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip()
            ]);
            
            return $this->paginatedResponse(
                $stockMovements,
                'Movimientos de stock filtrados recuperados exitosamente.',
                ['filters_applied' => $filters]
            );
        } catch (Exception $e) {
            Log::error('Error trying to retrieve filtered stock movements', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error al obtener los movimientos de stock filtrados',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,StockMovement $stockMovement): JsonResponse
    {
        try {
            $stockMovement->load(['product.category', 'order', 'movementType', 'orderDetail']);

            Log::info('Retrieved a stock movement', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse(
                $stockMovement,
                'Movimiento de stock obtenido exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to retrieve a stock movement', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'stock_movement_id' => $stockMovement->id ?? null,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error al obtener el movimiento de stock',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    // public const ALLOWED_SORT_FIELDS = [
    //     'order_id' => 'Pedido',
    //     'product_id' => 'Producto',
    //     'movement_type_id' => 'Tipo de movimiento',
    //     'created_at' => 'Fecha de creacion',
    // ];

    // public const ALLOWED_SORT_DIRECTIONS = [
    //     'asc' => 'Ascendente',
    //     'desc' => 'Descendente'
    // ];

    /**
     * Get filters to be used in the index view
     */
    public function getFilters(Request $request): JsonResponse
    {
        try {
            $orders = Order::all('id', 'code', 'contact_id', 'movement_type_id', 'subtotal', 'adjustment_amount', 'total_net', 'created_at')
                            ->makeHidden(['is_exportable', 'subtotal_as_currency', 'subtotal_as_currency', 'adjustment_as_currency', 'total_net_as_currency', 'contact']);
            $products = Product::all(['id', 'code', 'name'])
                                ->makeHidden(['stock_availability', 'sale_price_as_currency', 'is_empty_stock', 'is_low_stock']);
            $contacts = Contact::all('name', 'id', 'code', 'deleted_at')
                                ->makeHidden(['last_order', 'phone_number_info']);
            $movementTypes = MovementType::whereIn('id', [1, 2, 3,4])->get();
            $dateFrom = StockMovement::min('created_at');
            $dateTo = StockMovement::max('created_at');

            $data = [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'movement_types' => $movementTypes,
                'orders' => $orders,
                'products' => $products,
                'contacts' => $contacts,
                'sort_by' => FilterStockMovementsRequest::ALLOWED_SORT_FIELDS,
                'sort_direction' => FilterStockMovementsRequest::ALLOWED_SORT_DIRECTIONS
            ];

            Log::info('Retrieve filters for stock movements', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse(
                $data,
                'Datos para filtrar movimientos de stock obtenidos exitosamente'
            );
        } catch (Exception $e) {
            Log::error('Error trying to retrieve filters for stock movements', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Error al obtener los filtros',
                ['exception' => $e->getMessage(), 'line' => $e->getLine()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }
}