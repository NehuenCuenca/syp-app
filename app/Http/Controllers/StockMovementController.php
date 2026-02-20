<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Http\Traits\ApiResponseTrait;
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
    public function index(Request $request): JsonResponse
    {
        try {
            $query = StockMovement::with(['product', 'order', 'movementType', 'orderDetail']);

            // Filtros
            if ($request->filled('order_id')) {
                $query->where('order_id', $request->order_id);
            }

            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->filled('movement_type_id')) {
                $query->where('movement_type_id', $request->movement_type_id);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('movement_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('movement_date', '<=', $request->date_to);
            }

            $search = $request->get('search', '');
            if ($request->filled('search')) {
                $query->where(function ($q) use ($search) {
                        $q->whereRelation('product', 'code', 'like', "{$search}%")
                            ->orWhereRelation('product', 'name', 'like', "%{$search}%");
                });
            }


            // Ordenamiento
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');

            if (in_array($sortBy, array_keys(self::ALLOWED_SORT_FIELDS))) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                // Fallback a ordenamiento por defecto si el campo no es válido
                $query->orderBy('created_at', 'desc');
            }

            // Paginación
            $perPage = $request->get('per_page', 9);
            $stockMovements = $query->paginate($perPage);

            $filtersApplied = [
                // 'search' => $search,
                'sort_by' => $sortBy, 
                'sort_direction' => $sortDirection,
                'per_page' => $perPage,
                'page' => $request->integer('page', 1)
            ];

            Log::info('Retrieve filtered stock movements', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip()
            ]);
            
            return $this->paginatedResponse(
                $stockMovements,
                'Movimientos de stock filtrados recuperados exitosamente.',
                ['filters_applied' => $filtersApplied]
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

    public const ALLOWED_SORT_FIELDS = [
        'order_id' => 'Pedido',
        'product_id' => 'Producto',
        'movement_type_id' => 'Tipo de movimiento',
        'created_at' => 'Fecha de creacion',
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'asc' => 'Ascendente',
        'desc' => 'Descendente'
    ];

    /**
     * Get filters to be used in the index view
     */
    public function getFilters(Request $request): JsonResponse
    {
        try {
            $orders = Order::select('id', 'code', 'contact_id', 'movement_type_id', 'subtotal', 'adjustment_amount', 'total_net', 'created_at')->get();
            $products = Product::select('name', 'id', 'code', 'current_stock', 'min_stock_alert', 'deleted_at')->get();
            $movementTypes = MovementType::select('name', 'id')->get();
            $dateFrom = StockMovement::min('created_at');
            $dateTo = StockMovement::max('created_at');

            $data = [
                'orders' => $orders,
                'products' => $products,
                'movement_types' => $movementTypes,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort_by' => self::ALLOWED_SORT_FIELDS,
                'sort_direction' => self::ALLOWED_SORT_DIRECTIONS
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