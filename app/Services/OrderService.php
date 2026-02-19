<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\MovementType;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\StockMovement;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Create a new order with its details and stock movements
     *
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Resolve or create contact
            $contact = $this->resolveContact($data);
            
            // Create the order
            $order = Order::create([
                'contact_id' => $contact->id,
                'movement_type_id' => $data['movement_type_id'],
                'notes' => $data['notes'] ?? null,
                'adjustment_amount' => $data['adjustment_amount'] ?? 0,
            ]);

            if (!$order) {
                throw new Exception('No se pudo crear el pedido');
            }

            // Create order details
            $this->createOrderDetails($order, $data['order_details']);

            // Generate order code and calculate totals
            $this->updateOrderCodeAndTotals($order, $data['adjustment_amount'] ?? 0);

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'contact_id' => $contact->id,
            ]);

            return $order->load(['contact', 'orderDetails.product.category', 'movementType']);
        });
    }

    /**
     * Update an existing order
     *
     * @param Order $order
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function updateOrder(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            // Resolve or create contact
            $contact = $this->resolveContact($data);

            // Update order basic information
            $updated = $order->update([
                'contact_id' => $contact->id,
                'notes' => $data['notes'] ?? $order->notes,
                'adjustment_amount' => $data['adjustment_amount'] ?? $order->adjustment_amount,
            ]);

            if (!$updated) {
                throw new Exception('No se pudo actualizar el pedido');
            }

            // Update order details if provided
            if (isset($data['order_details']) && !empty($data['order_details'])) {
                $this->revertOrderStock($order);
                $this->deleteOrderDetailsAndMovements($order);
                $this->createOrderDetails($order, $data['order_details']);
                $this->updateOrderCodeAndTotals($order, $data['adjustment_amount'] ?? $order->adjustment_amount);
            } else {
                // Recalculate totals even if details weren't updated
                $this->updateOrderTotals($order);
            }

            Log::info('Order updated successfully', [
                'order_id' => $order->id,
                'contact_id' => $contact->id,
            ]);

            return $order->fresh()->load(['contact', 'orderDetails.product.category', 'movementType']);
        });
    }

    /**
     * Delete an order and revert all stock movements
     *
     * @param Order $order
     * @return bool
     * @throws Exception
     */
    public function deleteOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            // Revert stock movements
            $this->revertOrderStock($order);

            // Delete related records
            $this->deleteOrderDetailsAndMovements($order);

            // Delete the order
            $deleted = $order->delete();

            if (!$deleted) {
                throw new Exception('No se pudo eliminar el pedido');
            }

            Log::info('Order deleted successfully', [
                'order_id' => $order->id,
            ]);

            return true;
        });
    }

    /**
     * Resolve or create contact from request data
     *
     * @param array $data
     * @return Contact
     */
    protected function resolveContact(array $data): Contact
    {
        if (isset($data['contact_id']) && $data['contact_id']) {
            $contact = Contact::find($data['contact_id']);
            if (!$contact) {
                throw new Exception("Contacto con ID {$data['contact_id']} no encontrado");
            }
            return $contact;
        }

        if (isset($data['new_contact_name']) && $data['new_contact_name']) {
            return Contact::firstOrCreate(
                [ 'name' => $data['new_contact_name'] ],
                [
                    'name' => $data['new_contact_name'],
                    'contact_type' => 'Cliente',
                ]
            );
        }

        throw new Exception('Se debe proporcionar un contacto existente o crear uno nuevo');
    }

    /**
     * Create order details and associated stock movements
     *
     * @param Order $order
     * @param array $details
     * @return void
     * @throws Exception
     */
    protected function createOrderDetails(Order $order, array $details): void
    {
        if (empty($details)) {
            throw new Exception('No se proporcionaron detalles al pedido');
        }

        foreach ($details as $detail) {
            $product = Product::find($detail['id_product']);
            
            if (!$product) {
                throw new Exception("Producto con ID {$detail['id_product']} no encontrado");
            }

            // Calculate quantity to discount (handle stock limits for sales)
            $quantityToProcess = $this->calculateQuantityToProcess(
                $order,
                $product,
                $detail['quantity']
            );

            // Create order detail
            $orderDetail = OrderDetail::create([
                'id_order' => $order->id,
                'id_product' => $detail['id_product'],
                'quantity' => $quantityToProcess,
                'unit_price' => $detail['unit_price'],
                'percentage_applied' => $detail['percentage_applied'] ?? 0,
            ]);

            if (!$orderDetail) {
                throw new Exception('Error al crear detalle del pedido');
            }

            Log::info('Order detail created', [
                'order_detail_id' => $orderDetail->id,
                'product_id' => $detail['id_product'],
                'quantity' => $quantityToProcess,
            ]);

            // Update product price if it's a purchase
            if ($order->getIsPurchaseAttribute()) {
                $this->updateProductPurchasePrice(
                    $product,
                    $detail['unit_price'],
                    $detail['percentage_applied'] ?? 0
                );
            }

            // Create stock movement
            $this->createStockMovement($order, $orderDetail);
        }
    }

    /**
     * Calculate the quantity to process based on order type and available stock
     *
     * @param Order $order
     * @param Product $product
     * @param int $requestedQuantity
     * @return int
     */
    protected function calculateQuantityToProcess(Order $order, Product $product, int $requestedQuantity): int
    {
        // For sales, if requested quantity exceeds stock, use available stock
        if ($order->getIsSaleAttribute() && $requestedQuantity > $product->current_stock) {
            return $product->current_stock;
        }

        return $requestedQuantity;
    }

    /**
     * Update product purchase price and profit percentage
     *
     * @param Product $product
     * @param float $buyPrice
     * @param float $profitPercentage
     * @return void
     */
    protected function updateProductPurchasePrice(Product $product, float $buyPrice, float $profitPercentage): void
    {
        $product->update([
            'buy_price' => $buyPrice,
            'profit_percentage' => $profitPercentage,
        ]);

        Log::info('Product purchase price updated', [
            'product_id' => $product->id,
            'buy_price' => $buyPrice,
            'profit_percentage' => $profitPercentage,
        ]);
    }

    /**
     * Create stock movement for an order detail
     *
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @return StockMovement
     * @throws Exception
     */
    protected function createStockMovement(Order $order, OrderDetail $orderDetail): StockMovement
    {
        try {
            // Determine quantity sign based on order type
            $quantity = $order->getIsSaleAttribute() 
                ? -$orderDetail->quantity 
                : $orderDetail->quantity;

            // Generate movement notes
            $notes = $this->generateStockMovementNotes($order, $orderDetail);

            // Create stock movement
            $stockMovement = StockMovement::create([
                'id_product' => $orderDetail->id_product,
                'id_order' => $order->id,
                'id_order_detail' => $orderDetail->id,
                'movement_type_id' => $order->movement_type_id,
                'quantity_moved' => $quantity,
                'notes' => $notes,
            ]);

            if (!$stockMovement) {
                throw new Exception('No se pudo crear el movimiento de stock');
            }

            // Update product stock
            $product = Product::find($orderDetail->id_product);
            if (!$product) {
                throw new Exception("Producto con ID {$orderDetail->id_product} no encontrado");
            }

            $product->increment('current_stock', $quantity);

            Log::info('Stock movement created and product stock updated', [
                'stock_movement_id' => $stockMovement->id,
                'product_id' => $product->id,
                'quantity_moved' => $quantity,
            ]);

            return $stockMovement;
        } catch (Exception $e) {
            Log::error('Error creating stock movement', [
                'order_id' => $order->id,
                'order_detail_id' => $orderDetail->id,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Error al crear movimiento de stock: ' . $e->getMessage());
        }
    }

    /**
     * Generate notes for stock movement
     *
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @return string
     */
    protected function generateStockMovementNotes(Order $order, OrderDetail $orderDetail): string
    {
        $movementAction = $order->getIsSaleAttribute()
            ? "Vendí a {$orderDetail->line_subtotal_as_currency}"
            : "Compré a {$orderDetail->line_subtotal_as_currency}";

        if ($orderDetail->quantity > 1) {
            $movementAction .= " (x1: {$orderDetail->unit_price_with_discount_as_currency})";
        }

        return $movementAction;
    }

    /**
     * Revert stock movements for an order
     *
     * @param Order $order
     * @return void
     */
    protected function revertOrderStock(Order $order): void
    {
        if (!$order->orderDetails()->exists()) {
            return;
        }

        foreach ($order->orderDetails as $detail) {
            $quantityToRevert = StockMovement::where('id_order', $order->id)
                ->where('id_product', $detail->id_product)
                ->sum('quantity_moved');

            if ($quantityToRevert != 0) {
                $product = Product::find($detail->id_product);
                if ($product) {
                    // Revert the stock (opposite sign)
                    $product->decrement('current_stock', $quantityToRevert);

                    Log::info('Product stock reverted', [
                        'product_id' => $product->id,
                        'quantity_reverted' => $quantityToRevert,
                    ]);
                }
            }
        }
    }

    /**
     * Delete order details and stock movements
     *
     * @param Order $order
     * @return void
     */
    protected function deleteOrderDetailsAndMovements(Order $order): void
    {
        $order->stockMovements()->delete();
        $order->orderDetails()->delete();

        Log::info('Order details and stock movements deleted', [
            'order_id' => $order->id,
        ]);
    }

    /**
     * Generate order code and update totals
     *
     * @param Order $order
     * @param float $adjustmentAmount
     * @return void
     */
    protected function updateOrderCodeAndTotals(Order $order, float $adjustmentAmount): void
    {
        // Generate order code if not exists
        if (!$order->code) {
            $movementType = MovementType::find($order->movement_type_id);
            $orderCode = substr($movementType->name, 0, 1) . $order->id;
            $order->update(['code' => $orderCode]);
        }

        // Update totals
        $this->updateOrderTotals($order, $adjustmentAmount);
    }

    /**
     * Update order subtotal and total net
     *
     * @param Order $order
     * @param float|null $adjustmentAmount
     * @return void
     */
    protected function updateOrderTotals(Order $order, ?float $adjustmentAmount = null): void
    {
        $order->refresh();
        
        $subtotal = $order->orderDetails()->sum('line_subtotal');
        $adjustment = $adjustmentAmount ?? $order->adjustment_amount ?? 0;
        $totalNet = $subtotal + $adjustment;

        $order->update([
            'subtotal' => $subtotal,
            'total_net' => $totalNet,
        ]);

        Log::info('Order totals updated', [
            'order_id' => $order->id,
            'subtotal' => $subtotal,
            'adjustment_amount' => $adjustment,
            'total_net' => $totalNet,
        ]);
    }

    /**
     * Validate stock availability for sale order
     *
     * @param array $orderDetails
     * @param int $movementTypeId
     * @return array Array of validation errors (empty if valid)
     */
    public function validateStockAvailability(array $orderDetails, int $movementTypeId): array
    {
        $errors = [];
        $saleMovementTypeId = MovementType::firstWhere('name', Order::ORDER_TYPE_SALE)->id;

        if ($movementTypeId !== $saleMovementTypeId) {
            return $errors; // Only validate for sales
        }

        foreach ($orderDetails as $index => $detail) {
            $product = Product::find($detail['id_product']);
            if ($product && $product->current_stock < $detail['quantity']) {
                $errors["order_details.{$index}.quantity"] = 
                    "Stock insuficiente para el producto {$product->name}. Stock disponible: {$product->current_stock}";
            }
        }

        return $errors;
    }

    /**
     * Validate stock availability for order update
     *
     * @param Order $order
     * @param array $newOrderDetails
     * @return array Array of validation errors (empty if valid)
     */
    public function validateStockAvailabilityForUpdate(Order $order, array $newOrderDetails): array
    {
        $errors = [];

        if (!$order->getIsSaleAttribute()) {
            return $errors; // Only validate for sales
        }

        foreach ($newOrderDetails as $index => $newDetail) {
            $product = Product::find($newDetail['id_product']);
            if (!$product) {
                continue;
            }

            // Find existing order detail for this product
            $existingDetail = $order->orderDetails->where('id_product', $product->id)->first();

            // Calculate valid stock (current stock + previously ordered quantity if same product)
            $validStock = $existingDetail
                ? ($product->current_stock + $existingDetail->quantity)
                : $product->current_stock;

            if ($validStock < $newDetail['quantity']) {
                $errors["order_details.{$index}.quantity"] = 
                    "Stock insuficiente para el producto {$product->name}. Stock disponible: {$validStock}";
            }
        }

        return $errors;
    }
}