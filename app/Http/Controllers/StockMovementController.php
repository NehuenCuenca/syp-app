<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Requests\UpdateStockMovementRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\MovementType;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                500
            );
        }
    }

    /* public function create(): JsonResponse
    {
        try {
            $products = Product::with('category:id,name')
                ->select('id', 'name', 'current_stock', 'min_stock_alert', 'id_category')
                ->orderBy('name')
                ->get();

            $data = [
                'products' => $products,
                'increment_movement_types' => MovementType::where('increase_stock', true)->get(),
                'decrement_movement_types' => MovementType::where('increase_stock', false)->get(),
            ];

            return $this->successResponse(
                $data,
                'Datos para crear movimiento de stock obtenidos exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener datos para crear movimiento de stock: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Error al obtener los datos necesarios para crear el movimiento de stock',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    } */

    /* public function edit(StockMovement $stockMovement): JsonResponse
    {
        try {
            $products = Product::with('category:id,name')
                ->select('id', 'name', 'current_stock', 'min_stock_alert', 'id_category')
                ->orderBy('name')
                ->get();

            $data = [
                'stock_movement' => $stockMovement->load(['product.category', 'order.contact']),
                'increment_movement_types' => MovementType::where('increase_stock', true)->get(),
                'decrement_movement_types' => MovementType::where('increase_stock', false)->get(),
                'products' => $products,
            ];

            return $this->successResponse(
                $data,
                'Datos para editar el movimiento de stock obtenidos exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error al obtener datos para editar movimiento de stock: ' . $e->getMessage(), [
                'stock_movement_id' => $stockMovement->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Error al obtener los datos necesarios para editar el movimiento de stock',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    } */

    /**
     * Store a newly created resource in storage.
     */
    /* public function store(StoreStockMovementRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();

            // Obtener el producto
            $product = Product::findOrFail($validated['id_product']);

            // Obtener el tipo de movimiento
            $movementType = MovementType::find($validated['id_movement_type']);
            if (!$movementType) {
                DB::rollBack();
                return $this->errorResponse(
                    'Tipo de movimiento no válido',
                    ['id_movement_type' => $validated['id_movement_type']]
                );
            }

            // Determinar si es incremento o decremento según el tipo de movimiento
            $isIncrement = in_array($validated['id_movement_type'], MovementType::getIncrementMovementTypes());
            $quantityMoved = $validated['quantity_moved'];

            // Verificar stock suficiente para decrementos
            if (!$isIncrement && $product->current_stock < $quantityMoved) {
                DB::rollBack();
                return $this->errorResponse(
                    'Stock insuficiente',
                    ['current_stock' => $product->current_stock, 'required' => $quantityMoved],
                    ['product_id' => $product->id, 'product_name' => $product->name]
                );
            }

            // Crear el movimiento con la cantidad con signo correspondiente
            $stockMovement = StockMovement::create([
                'id_product' => $validated['id_product'],
                'id_order' => $validated['id_order'] ?? null,
                'id_order_detail' => $validated['id_order_detail'] ?? null,
                'id_movement_type' => $movementType->id,
                'quantity_moved' => $isIncrement ? $quantityMoved : -$quantityMoved,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Actualizar el stock del producto
            if ($isIncrement) {
                $product->increment('current_stock', $quantityMoved);
            } else {
                $product->decrement('current_stock', $quantityMoved);
            }

            DB::commit();

            // Cargar las relaciones para la respuesta
            $stockMovement->load(['product.category', 'order']);

            return $this->createdResponse(
                $stockMovement,
                'Movimiento de stock creado exitosamente'
            );

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear movimiento de stock: ' . $e->getMessage(), [
                'request_data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Error al crear el movimiento de stock',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    } */

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
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    /* public function update(UpdateStockMovementRequest $request, StockMovement $stockMovement): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $product = Product::findOrFail($stockMovement->id_product);

            // Revertir el movimiento anterior
            $product->current_stock -= $stockMovement->quantity_moved;

            // Determinar si es incremento o decremento según el nuevo tipo de movimiento
            $isIncrement = in_array($validated['id_movement_type'], MovementType::getIncrementMovementTypes());
            $quantityMoved = $validated['quantity_moved'];

            // Verificar stock suficiente para decrementos
            if (!$isIncrement && $product->current_stock < $quantityMoved) {
                DB::rollBack();
                return $this->errorResponse(
                    'Stock insuficiente después de revertir el movimiento anterior',
                    ['available_stock' => $product->current_stock, 'required' => $quantityMoved],
                    ['product_id' => $product->id, 'product_name' => $product->name]
                );
            }

            // Obtener el tipo de movimiento
            $movementType = MovementType::find($validated['id_movement_type']);
            if (!$movementType) {
                DB::rollBack();
                return $this->errorResponse(
                    'Tipo de movimiento no válido',
                    ['id_movement_type' => $validated['id_movement_type']]
                );
            }

            // Actualizar el movimiento con la nueva cantidad con signo
            $stockMovement->update([
                'id_movement_type' => $movementType->id,
                'quantity_moved' => $isIncrement ? $quantityMoved : -$quantityMoved,
                'notes' => $validated['notes'] ?? $stockMovement->notes
            ]);

            // Aplicar el nuevo movimiento
            if ($isIncrement) {
                $product->current_stock += $quantityMoved;
            } else {
                $product->current_stock -= $quantityMoved;
            }

            $product->save();

            DB::commit();

            $stockMovement->load(['product.category', 'order']);

            return $this->successResponse(
                $stockMovement,
                'Movimiento de stock actualizado exitosamente'
            );

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error al actualizar movimiento de stock: ' . $e->getMessage(), [
                'stock_movement_id' => $stockMovement->id ?? null,
                'request_data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Error al actualizar el movimiento de stock',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    } */

    /**
     * Remove the specified resource from storage.
     */
    /* public function destroy(StockMovement $stockMovement): JsonResponse
    {
        try {
            DB::beginTransaction();

            if($stockMovement->order) {
                return $this->errorResponse(
                    'No se puede eliminar un movimiento de stock que tiene un pedido asociado',
                    ['stock_movement_id' => $stockMovement->id, 'order_id' => $stockMovement->order->id]
                );
            }

            $product = Product::findOrFail($stockMovement->id_product);
            $stockMovementId = $stockMovement->id;

            // Revertir el movimiento de stock
            $product->current_stock -= $stockMovement->quantity_moved;
            $product->save();

            // Eliminar el registro
            $stockMovement->delete();

            DB::commit();

            return $this->deletedResponse(
                $stockMovementId,
                'Movimiento de stock eliminado exitosamente',
                true // hard delete
            );

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error al eliminar movimiento de stock: ' . $e->getMessage(), [
                'stock_movement_id' => $stockMovement->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Error al eliminar el movimiento de stock',
                ['exception' => $e->getMessage()],
                [],
                500
            );
        }
    } */

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
            $products = Product::select('name', 'id', 'code', 'current_stock', 'min_stock_alert')->get();
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
                500
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
                500
            );
        }
    }
}