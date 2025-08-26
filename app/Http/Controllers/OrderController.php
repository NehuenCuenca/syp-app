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
        $orders = Order::with(['contact', 'userCreator', 'orderDetails.product.category'])
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

        $products = Product::with('category:id,name')
            ->select('id', 'sku', 'name', 'current_stock', 'buy_price', 'sale_price', 'id_category')
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
                'order_type' => $request->order_type,
                'order_status' => $request->order_status ?? 'Pendiente',
                'notes' => $request->notes,
                'total_net' => $request->total_net ?? 0,
            ]);

            $totalDiscount = 0;
            $totalGross = 0;
            $totalNet = 0;

            // Crear los detalles del pedido
            foreach ($request->order_details as $detail) {
                $lineGrossSubtotal = $detail['quantity'] * $detail['unit_price_at_order'];
                $totalDiscount += $detail['quantity'] * $detail['unit_price_at_order'] * $detail['discount_percentage_by_unit'];
                $totalGross += $lineGrossSubtotal;
                $product = Product::find($detail['id_product']);
                $stockToDiscount = ($detail['quantity'] >= $product->current_stock) ? $product->current_stock : $detail['quantity'];

                $detailRecord = OrderDetail::create([
                    'id_order' => $order->id,
                    'id_product' => $detail['id_product'],
                    'quantity' => $stockToDiscount,
                    'unit_price_at_order' => $detail['unit_price_at_order'],
                    'discount_percentage_by_unit' => $detail['discount_percentage_by_unit'] ?? 0,
                ]);

                if( $order->getIsPurchaseAttribute() ){
                    $product->update(['buy_price' => $detail['unit_price_at_order']]);
                }

                if( $order->getIsSaleAttribute() ){
                    $this->createStockMovement($order, $detailRecord);
                }
            }

            $totalNet = (float)($request->filled('total_net') ? $request->integer('total_net') : ($totalGross - $totalDiscount));
            $order->update(['total_net' => $totalNet ]);

            DB::commit();

            $order->append(['show_valid_transitions']);
            
            return response()->json([
                'message' => 'Pedido creado exitosamente',
                'order' => $order->load(['contact', 'userCreator', 'orderDetails.product.category'])
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
            $order->load(['contact', 'userCreator', 'orderDetails.product.category'])
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order->load(['contact', 'userCreator', 'orderDetails.product.category']);

        $contacts = Contact::select('id', 'company_name', 'contact_name', 'contact_type')
            ->orderBy('company_name')
            ->get();

        $products = Product::with('category:id,name')
            ->select('id', 'sku', 'name', 'current_stock', 'sale_price', 'buy_price', 'id_category')
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
                'actual_delivery_date' => $request->actual_delivery_date,
                'notes' => $request->notes,
                'total_net' => $request->total_net ?? $order->total_net
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
        $fromPendingToCompleted  = $oldStatus === 'Pendiente' && $newStatus === 'Completado'; //Venta: -                     //Compra: createStockMovement 
        $fromPendingToCanceled   = $oldStatus === 'Pendiente' && $newStatus === 'Cancelado';  //Venta: revertStockMovements  //Compra: -
        $fromCompletedToPending  = $oldStatus === 'Completado' && $newStatus === 'Pendiente'; //Venta: -                     //Compra: revertStockMovements
        $fromCompletedToReturned = $oldStatus === 'Completado' && $newStatus === 'Devuelto';  //Venta: createReturnMovements //Compra: createReturnMovements
        $fromCanceledToPending   = $oldStatus === 'Cancelado' && $newStatus === 'Pendiente';  //Venta: createStockMovement   //Compra: -
        $fromReturnedToPending   = $oldStatus === 'Devuelto' && $newStatus === 'Pendiente';   //Venta: createStockMovement   //Compra: -
        $fromReturnedToCompleted = $oldStatus === 'Devuelto' && $newStatus === 'Completado';  //Venta: createStockMovement   //Compra: createStockMovement

        if($order->getIsSaleAttribute()){
            if($fromPendingToCanceled){
                $this->revertStockMovements($order);
            }

            elseif ($fromCompletedToReturned){
                $this->createReturnMovements($order);
            }
            
            elseif ($fromCanceledToPending || $fromReturnedToPending || $fromReturnedToCompleted){
                foreach ($order->orderDetails as $detail) {
                    $this->createStockMovement($order, $detail);
                }
            }
        }  
        
        if($order->getIsPurchaseAttribute()){
            if ($fromPendingToCompleted || $fromReturnedToCompleted) {
                foreach ($order->orderDetails as $detail) {
                    $this->createStockMovement($order, $detail);
                }
            }
            
            elseif ($fromCompletedToPending) {
                $this->revertStockMovements($order);
            }
            
            elseif ($fromCompletedToReturned) {
                $this->createReturnMovements($order);
            }
        }
    }

    /**
     * Crear movimiento de stock para un detalle del pedido
     */
    private function createStockMovement(Order $order, OrderDetail $detail)
    {
        $movementType = $order->getIsPurchaseAttribute() ? 'Compra_Entrante' : 'Venta_Saliente';
        $quantity = ($order->getIsPurchaseAttribute()) ? $detail->quantity : -$detail->quantity;

        StockMovement::create([
            'id_product' => $detail->id_product,
            'id_order' => $order->id,
            'id_user_responsible' => $order->id_user_creator,
            'movement_type' => $movementType,
            'quantity_moved' => $quantity,
            'movement_date' => now(),
            'notes' => "Movimiento automático por pedido #{$order->id}",
        ]);

        // Actualizar stock actual del producto si es una compra entrante
        $product = Product::find($detail->id_product);
        $product->increment('current_stock', $quantity);
    }

    /**
     * 
     * Revertir movimientos de stock de un pedido
     */
    private function revertStockMovements(Order $order)
    {
        $orderDetails = OrderDetail::where('id_order', $order->id)->get();
        
        foreach ($orderDetails as $detail) {
            $movementType = $order->getIsPurchaseAttribute() ? 'Ajuste_Negativo' : 'Ajuste_Positivo';
            $quantity = $order->order_type === 'Compra_Entrante' ? -$detail['quantity'] : $detail['quantity'];

            // Crear movimiento inverso
            StockMovement::create([
                'id_product' => $detail->id_product,
                'id_order' => $order->id,
                'id_user_responsible' => auth()->id() ?? $order->id_user_creator,
                'movement_type' => $movementType,
                'quantity_moved' => $quantity,
                'movement_date' => now(),
                'notes' => "Reversión de movimiento por cambio de estado del pedido #{$order->id}",
            ]);

            // Actualizar stock
            $product = Product::find($detail->id_product);
            $product->decrement('current_stock', $quantity);
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


    public const ALLOWED_SORT_FIELDS  = [
            'id', 'created_at', 
            'actual_delivery_date', 'order_type', 
            'order_status', 'total_net'
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
        $query = Order::with(['contact', 'userCreator', 'orderDetails.product.category']);

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
            'id', 'created_at', 
            'actual_delivery_date', 'order_type',
            'order_status', 'total_net'
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