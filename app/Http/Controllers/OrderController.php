<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Contact;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\StockMovement;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['contact', 'userCreator', 'orderDetails.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return response()->json($orders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contacts = Contact::select('id', 'company_name', 'contact_name', 'contact_type')
            ->orderBy('company_name')
            ->get();

        $products = Product::select('id', 'sku', 'name', 'current_stock', 'suggested_sale_price', 'avg_purchase_price')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'order_types' => Order::getOrderTypes(),
                'order_statuses' => Order::getOrderStatuses(),
                'contacts' => $contacts,
                'products' => $products
            ],
            'message' => 'Datos para crear pedido obtenidos exitosamente'
        ]);
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
                'estimated_delivery_date' => $request->estimated_delivery_date,
                'order_type' => $request->order_type,
                'order_status' => $request->order_status ?? 'Pendiente',
                'notes' => $request->notes,
                'total_net' => $request->total_net ?? 0,
            ]);

            $totalGross = 0;
            $totalTaxes = 0;

            // Crear los detalles del pedido
            foreach ($request->order_details as $detail) {
                $lineSubtotal = $detail['quantity'] * $detail['unit_price_at_order'];
                $totalGross += $lineSubtotal;
                $product = Product::find($detail['id_product']);
                $stockToDiscount = ($detail['quantity'] >= $product->current_stock) ? $product->current_stock : $detail['quantity'];

                OrderDetail::create([
                    'id_order' => $order->id,
                    'id_product' => $detail['id_product'],
                    'quantity' => $stockToDiscount,
                    'unit_price_at_order' => $detail['unit_price_at_order'],
                    'line_subtotal' => $lineSubtotal,
                ]);

                if( $order->getIsSaleAttribute() ){
                    $this->createStockMovement($order, [
                        'id_product' => $detail['id_product'],
                        'quantity' => -$stockToDiscount
                    ]);
                }
            }

            // Calcular totales (asumiendo 21% de IVA)
            //$totalTaxes = $totalGross * 0.21;
            $totalNet = $totalGross + $totalTaxes;

            // Actualizar totales del pedido
            $order->update([
                'total_gross' => $totalGross,
                'total_taxes' => $totalTaxes,
                'total_net' => ($request->missing('total_net')) ? $totalNet : $request->total_net,
            ]);

            DB::commit();

            $order->append(['show_valid_transitions']);
            
            return response()->json([
                'message' => 'Pedido creado exitosamente',
                'order' => $order->load(['contact', 'userCreator', 'orderDetails.product']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear el pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->json(
            $order->load(['contact', 'userCreator', 'orderDetails.product'])
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order->load(['contact', 'userCreator', 'orderDetails.product']);

        $contacts = Contact::select('id', 'company_name', 'contact_name', 'contact_type')
            ->orderBy('company_name')
            ->get();

        $products = Product::select('id', 'sku', 'name', 'current_stock', 'suggested_sale_price', 'avg_purchase_price')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'contacts' => $contacts,
                'products' => $products,
                'order_types' => Order::getOrderTypes(),
                'order_statuses' => Order::getOrderStatuses(),
            ],
            'message' => 'Datos para editar pedido obtenidos exitosamente'
        ]);
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
            $order->update([
                'id_contact' => $request->id_contact,
                'estimated_delivery_date' => $request->estimated_delivery_date,
                'actual_delivery_date' => $request->actual_delivery_date,
                'notes' => $request->notes,
                'total_gross' => $request->total_gross,
                'total_taxes' => $request->total_taxes,
                'total_net' => $request->total_net
            ]);

            // Manejar cambios de estado y movimientos de stock
            if( $newStatus !== $oldStatus ){
                $order->update(['order_status' => $newStatus]);
                $this->handleStatusChange($order, $oldStatus, $newStatus);
            }

            DB::commit();

            $order->append(['show_valid_transitions']);
            
            return response()->json([
                'message' => 'Pedido actualizado exitosamente',
                'order' => $order->load(['contact', 'userCreator', 'orderDetails.product'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        DB::beginTransaction();
        
        try {
            
            if ( $order->orderDetails()->exists() ) {
                foreach ($order->orderDetails->all() as $detail) {
                    $quantityToRevert = StockMovement::where('id_order', $order->id)
                        ->where('id_product', $detail->id_product)
                        ->sum('quantity_moved');

                    // Actualizar stock
                    $product = Product::find($detail->id_product);
                    $product->decrement('current_stock', $quantityToRevert);
                }
            }
           
            $order->stockMovements()->delete();
            $order->orderDetails()->delete();            
            $order->delete();

            DB::commit();
            
            return response()->json([
                'message' => 'Pedido eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar el pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manejar cambios de estado y movimientos de stock
     */
    private function handleStatusChange(Order $order, string $oldStatus, string $newStatus)
    {
        if($order->getIsSaleAttribute()){
            // Si cambia de completado a no completado
            if ($oldStatus === 'Completado' && $newStatus !== 'Completado') {
                $this->revertStockMovements($order);
            }
            // Si cambia a devuelto desde completado
            elseif ($oldStatus === 'Completado' && $newStatus === 'Devuelto') {
                $this->createReturnMovements($order);
            }
        }  
        
        if($order->getIsPurchaseAttribute()){
            // Si cambia de no completado a completado
            if ($oldStatus !== 'Completado' && $newStatus === 'Completado') {
                foreach ($order->orderDetails as $detail) {
                    $this->createStockMovement($order, [
                        'id_product' => $detail->id_product,
                        'quantity' => $detail->quantity
                    ]);
                }
            }
            // Si cambia de completado a no completado
            elseif ($oldStatus === 'Completado' && $newStatus !== 'Completado') {
                $this->revertStockMovements($order);
            }
            // Si cambia a devuelto desde completado
            elseif ($oldStatus === 'Completado' && $newStatus === 'Devuelto') {
                $this->createReturnMovements($order);
            }
        }
    }

    /**
     * Crear movimiento de stock para un detalle del pedido
     */
    private function createStockMovement(Order $order, array $detail)
    {
        $movementType = $order->order_type === 'Compra_Entrante' ? 'Compra_Entrante' : 'Venta_Saliente';
        // $quantity = $order->order_type === 'Compra_Entrante' ? $detail['quantity'] : -$detail['quantity'];

        StockMovement::create([
            'id_product' => $detail['id_product'],
            'id_order' => $order->id,
            'id_user_responsible' => $order->id_user_creator,
            'movement_type' => $movementType,
            'quantity_moved' => $detail['quantity'],
            'movement_date' => now(),
            'notes' => "Movimiento automático por pedido #{$order->id}",
        ]);

        // Actualizar stock actual del producto si es una compra entrante
        $product = Product::find($detail['id_product']);
        $product->increment('current_stock', $detail['quantity']);
    }

    /**
     * 
     * Revertir movimientos de stock de un pedido
     */
    private function revertStockMovements(Order $order)
    {
        $movements = StockMovement::where('id_order', $order->id)->get();
        
        foreach ($movements as $movement) {
            $movementType = $order->getIsPurchaseAttribute() ? 'Ajuste_Negativo' : 'Ajuste_Positivo';

            // Crear movimiento inverso
            StockMovement::create([
                'id_product' => $movement->id_product,
                'id_order' => $order->id,
                'id_user_responsible' => auth()->id() ?? $order->id_user_creator,
                'movement_type' => $movementType,
                'quantity_moved' => -$movement->quantity_moved,
                'movement_date' => now(),
                'notes' => "Reversión de movimiento por cambio de estado del pedido #{$order->id}",
            ]);

            // Actualizar stock
            $product = Product::find($movement->id_product);
            $product->decrement('current_stock', $movement->quantity_moved);
        }
    }

    /**
     * Crear movimientos de devolución
     */
    private function createReturnMovements(Order $order)
    {
        $returnType = $order->order_type === 'Compra_Entrante' ? 'Devolucion_Proveedor' : 'Devolucion_Cliente';
        
        foreach ($order->orderDetails as $detail) {
            $quantity = $order->order_type === 'Compra_Entrante' ? -$detail->quantity : $detail->quantity;
            
            StockMovement::create([
                'id_product' => $detail->id_product,
                'id_order' => $order->id,
                'id_user_responsible' => auth()->id() ?? $order->id_user_creator,
                'movement_type' => $returnType,
                'quantity_moved' => $quantity,
                'movement_date' => now(),
                'notes' => "Devolución del pedido #{$order->id}",
            ]);

            // Actualizar stock
            $product = Product::find($detail->id_product);
            $product->increment('current_stock', $quantity);
        }
    }

    /**
     * 
     * Crea movimientos de stock de un pedido que fue eliminado
     */
    /* private function createDeletedStockMovements(Order $order)
    {
        $movements = StockMovement::where('id_order', $order->id)->get();
        
        foreach ($movements as $movement) {
            $movementType = $order->getIsPurchaseAttribute() ? 'Ajuste_Negativo' : 'Ajuste_Positivo';

            // Crear movimiento inverso
            StockMovement::create([
                'id_product' => $movement->id_product,
                'id_order' => null,
                'id_user_responsible' => auth()->id() ?? $order->id_user_creator,
                'movement_type' => $movementType,
                'quantity_moved' => -$movement->quantity_moved,
                'movement_date' => now(),
                'notes' => "Reversión de movimiento por cambio de estado del pedido #{$order->id}",
            ]);

            // Actualizar stock
            $product = Product::find($movement->id_product);
            $product->decrement('current_stock', $movement->quantity_moved);
        }
    } */

    public const ALLOWED_SORT_FIELDS  = [
            'id', 'created_at', 'estimated_delivery_date', 
            'actual_delivery_date', 'order_type', 'order_status', 
            'total_gross', 'total_taxes', 'total_net'
    ];
    public const ALLOWED_SORT_DIRECTIONS = ['asc', 'desc'];


    /**
     * Get filters to be used in the index view
     */
    public function getFilters()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'order_types' => Order::getOrderTypes(),
                'order_statuses' => Order::getOrderStatuses(),
                'contacts' => Contact::select('id', 'company_name', 'contact_type')->get(),
                'date_from' => Order::min('created_at'),
                'date_to' => Order::max('created_at'),
                'sort_by' => self::ALLOWED_SORT_FIELDS,
                'sort_direction' => self::ALLOWED_SORT_DIRECTIONS
            ],
            'message' => 'Datos para filtrar pedidos obtenidos exitosamente'
        ]);
    }
    /**
     * Get filtered and ordered orders
     */
    public function getFilteredOrders(Request $request)
    {
        $query = Order::with(['contact', 'userCreator', 'orderDetails.product']);

        // Filtros
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('contact_id')) {
            $query->where('id_contact', $request->contact_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('contact', function ($contactQuery) use ($search) {
                      $contactQuery->where('company_name', 'like', "%{$search}%")
                                 ->orWhere('contact_name', 'like', "%{$search}%");
                  });
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = [
            'id', 'created_at', 'estimated_delivery_date', 
            'actual_delivery_date', 'order_type', 'order_status', 
            'total_gross', 'total_taxes', 'total_net'
        ];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Paginación
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json($orders);
    }
}