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
    public function index(): JsonResponse
    {
        try {
            $stockMovements = StockMovement::with(['product', 'order', 'movementType', 'orderDetail'])
                ->orderBy('created_at', 'desc')
                ->get();

            $meta = ['total' => $stockMovements->count()];

            return $this->successResponse(
                $stockMovements, 
                'Todos los movimientos de stock recuperados exitosamente.', 
                $meta
            );

        } catch (Exception $e) {
            Log::error('Error al obtener movimientos de stock: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Error al obtener los movimientos de stock',
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
    public function show(StockMovement $stockMovement): JsonResponse
    {
        try {
            $stockMovement->load(['product.category', 'order', 'movementType', 'orderDetail']);

            return $this->successResponse(
                $stockMovement,
                'Movimiento de stock obtenido exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener movimiento de stock: ' . $e->getMessage(), [
                'stock_movement_id' => $stockMovement->id ?? null,
                'trace' => $e->getTraceAsString()
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
        'id_order' => 'Pedido',
        'id_product' => 'Producto',
        'id_movement_type' => 'Tipo de movimiento',
        'created_at' => 'Fecha de creacion',
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'asc' => 'Ascendente',
        'desc' => 'Descendente'
    ];

    /**
     * Get filters to be used in the index view
     */
    public function getFilters(): JsonResponse
    {
        try {
            $orders = Order::select('id', 'code', 'id_contact', 'id_movement_type', 'total_net', 'created_at')->get();
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

            return $this->successResponse(
                $data,
                'Datos para filtrar movimientos de stock obtenidos exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener filtros de movimientos de stock: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
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

    /**
     * Get filtered and ordered movements
     */
    public function getFilteredMovements(Request $request): JsonResponse
    {
        try {
            $query = StockMovement::with(['product', 'order', 'movementType', 'orderDetail']);

            // Filtros
            if ($request->filled('id_order')) {
                $query->where('id_order', $request->id_order);
            }

            if ($request->filled('id_product')) {
                $query->where('id_product', $request->id_product);
            }

            if ($request->filled('id_movement_type')) {
                $query->where('id_movement_type', $request->id_movement_type);
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
            
            return $this->paginatedResponse(
                $stockMovements,
                'Movimientos de stock filtrados recuperados exitosamente.',
                ['filters_applied' => $filtersApplied]
            );

        } catch (Exception $e) {
            Log::error('Error al obtener movimientos de stock filtrados: ' . $e->getMessage(), [
                'filters' => $request->all(),
                'trace' => $e->getTraceAsString()
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
}