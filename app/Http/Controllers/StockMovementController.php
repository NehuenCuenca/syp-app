<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Requests\UpdateStockMovementRequest;
use App\Models\MovementType;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $stockMovements = StockMovement::with(['product.category', 'order', 'userResponsible'])
            ->orderBy('movement_date', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $stockMovements
        ]);
    }

    public function create()
    {
        $orders = Order::with('contact:id,company_name')
                        ->select('id', 'id_contact', 'order_type', 'total_net', 'created_at')
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();

        $products = Product::with('category:id,name')
            ->select('id', 'sku', 'name', 'current_stock', 'id_category')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders,
                'products' => $products,
                'increment_movement_types' => MovementType::getIncrementMovementTypes(),
                'decrement_movement_types' => MovementType::getDecrementMovementTypes(),
            ],
            'message' => 'Datos para crear movimiento de stock obtenidos exitosamente'
        ]);
    }

    public function edit(StockMovement $stockMovement)
    {

        $orders = Order::with('contact:id,company_name')
                        ->select('id', 'id_contact', 'order_type', 'total_net', 'created_at')
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();

        $products = Product::with('category:id,name')
            ->select('id', 'sku', 'name', 'current_stock', 'buy_price', 'sale_price', 'id_category')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stock_movement' => $stockMovement->load(['product.category', 'order.contact']),
                'increment_movement_types' => MovementType::getIncrementMovementTypes(),
                'decrement_movement_types' => MovementType::getDecrementMovementTypes(),
                'orders' => $orders,
                'products' => $products,
            ],
            'message' => 'Datos para editar el movimiento de stock obtenidos exitosamente'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockMovementRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Obtener el producto
            $product = Product::findOrFail($validated['id_product']);

            // Determinar si es incremento o decremento según el tipo de movimiento
            $isIncrement = in_array($validated['movement_type'], MovementType::getIncrementMovementTypes());
            $quantityMoved = $validated['quantity_moved'];

            // Verificar stock suficiente para decrementos
            if (!$isIncrement && $product->current_stock < $quantityMoved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Stock actual: ' . $product->current_stock
                ], 400);
            }

            // Crear el movimiento con la cantidad con signo correspondiente
            $stockMovement = StockMovement::create([
                'id_product' => $validated['id_product'],
                'id_order' => $validated['id_order'] ?? null,
                'id_user_responsible' => Auth::id(),
                'id_movement_type' => MovementType::where('name', $validated['movement_type'])->first()->id,
                'quantity_moved' => $isIncrement ? $quantityMoved : -$quantityMoved,
                'external_reference' => $validated['external_reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'movement_date' => now()
            ]);

            // Actualizar el stock del producto
            if ($isIncrement) {
                $product->increment('current_stock', $quantityMoved);
            } else {
                $product->decrement('current_stock', $quantityMoved);
            }

            DB::commit();

            // Cargar las relaciones para la respuesta
            $stockMovement->load(['product.category', 'order', 'userResponsible']);

            return response()->json([
                'success' => true,
                'message' => 'Movimiento de stock creado exitosamente',
                'data' => $stockMovement
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el movimiento de stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement): JsonResponse
    {
        $stockMovement->load(['product.category', 'order', 'userResponsible']);

        return response()->json([
            'success' => true,
            'data' => $stockMovement
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockMovementRequest $request, StockMovement $stockMovement): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $product = Product::findOrFail($stockMovement->id_product);

            // Revertir el movimiento anterior
            $product->current_stock -= $stockMovement->quantity_moved;

            // Determinar si es incremento o decremento según el nuevo tipo de movimiento
            $isIncrement = in_array($validated['movement_type'], MovementType::getIncrementMovementTypes());
            $quantityMoved = $validated['quantity_moved'];

            // Verificar stock suficiente para decrementos
            if (!$isIncrement && $product->current_stock < $quantityMoved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente después de revertir el movimiento anterior. Stock disponible: ' . $product->current_stock
                ], 400);
            }

            // Actualizar el movimiento con la nueva cantidad con signo
            $stockMovement->update([
                'id_movement_type' => MovementType::where('name', $validated['movement_type'])->first()->id,
                'quantity_moved' => $isIncrement ? $quantityMoved : -$quantityMoved,
                'external_reference' => $validated['external_reference'] ?? $stockMovement->external_reference,
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

            $stockMovement->load(['product.category', 'order', 'userResponsible']);

            return response()->json([
                'success' => true,
                'message' => 'Movimiento de stock actualizado exitosamente',
                'data' => $stockMovement
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el movimiento de stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement): JsonResponse
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($stockMovement->id_product);

            // Revertir el movimiento de stock
            $product->current_stock -= $stockMovement->quantity_moved;
            $product->save();

            // Eliminar el registro
            $stockMovement->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Movimiento de stock eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el movimiento de stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public const ALLOWED_SORT_FIELDS  = [
            'id_order' => 'Pedido',
            'id_product' => 'Producto',
            'id_movement_type' => 'Tipo de movimiento',
            'movement_date' => 'Fecha del movimiento',
    ];
    public const ALLOWED_SORT_DIRECTIONS = [
        'asc' => 'Ascendente',
        'desc' => 'Descendente'
    ];

    /**
     * Get filters to be used in the index view
     */
    public function getFilters()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'orders' => Order::all()->select('order_alias', 'id'),
                'products' => Product::all()->select('name', 'sku', 'id'),
                'movement_types' => MovementType::all()->select('name', 'id'),
                'date_from' => StockMovement::min('created_at'),
                'date_to' => StockMovement::max('created_at'),
                'sort_by' => self::ALLOWED_SORT_FIELDS,
                'sort_direction' => self::ALLOWED_SORT_DIRECTIONS
            ],
            'message' => 'Datos para filtrar pedidos obtenidos exitosamente'
        ]);
    }

    /**
     * Get filtered and ordered movements
     */
    public function getFilteredMovements(Request $request)
    {
        $query = StockMovement::with(['product', 'movementType', 'order']);

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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('external_reference', 'like', "%{$search}%")
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
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json($orders);
    }
}