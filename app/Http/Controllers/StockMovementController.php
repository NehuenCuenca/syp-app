<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Requests\UpdateStockMovementRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $stockMovements = StockMovement::with(['product', 'order', 'userResponsible'])
            ->orderBy('movement_date', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $stockMovements
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
            $incrementTypes = ['Compra_Entrante', 'Devolucion_Cliente', 'Ajuste_Positivo'];
            $decrementTypes = ['Venta_Saliente', 'Devolucion_Proveedor', 'Ajuste_Negativo'];

            $isIncrement = in_array($validated['movement_type'], $incrementTypes);
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
                'movement_type' => $validated['movement_type'],
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
            $stockMovement->load(['product', 'order', 'userResponsible']);

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
        $stockMovement->load(['product', 'order', 'userResponsible']);

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
            $incrementTypes = ['Compra_Entrante', 'Devolucion_Cliente', 'Ajuste_Positivo'];
            $isIncrement = in_array($validated['movement_type'], $incrementTypes);
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
                'movement_type' => $validated['movement_type'],
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

            $stockMovement->load(['product', 'order', 'userResponsible']);

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
}