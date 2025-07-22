<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    // protected $primaryKey = 'id_order_detail';

    /**
     * The table associated with the model.
     */
    protected $table = 'order_details';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_order',
        'id_product',
        'quantity',
        'unit_price_at_order',
        'line_subtotal',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price_at_order' => 'decimal:2',
        'line_subtotal' => 'decimal:2',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * Relationship: OrderDetail belongs to an Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    /**
     * Relationship: OrderDetail belongs to a Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    /**
     * Get the stock movements related to this order detail.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'id_order', 'id_order')
            ->where('id_product', $this->id_product);
    }

    /**
     * Scope: Filter by order
     */
    public function scopeForOrder($query, int $orderId)
    {
        return $query->where('id_order', $orderId);
    }

    /**
     * Scope: Filter by product
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('id_product', $productId);
    }

    /**
     * Accessor: Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price_at_order, 2, ',', '.');
    }

    /**
     * Accessor: Get formatted line subtotal
     */
    public function getFormattedLineSubtotalAttribute(): string
    {
        return '$' . number_format($this->line_subtotal, 2, ',', '.');
    }

    /**
     * Accessor: Get product name
     */
    public function getProductNameAttribute(): string
    {
        return $this->product?->name ?? 'Producto no encontrado';
    }

    /**
     * Accessor: Get product SKU
     */
    public function getProductSkuAttribute(): string
    {
        return $this->product?->sku ?? 'SKU no encontrado';
    }

    /**
     * Accessor: Check if quantity is valid based on stock
     */
    public function getIsQuantityValidAttribute(): bool
    {
        if (!$this->product) {
            return false;
        }

        // Para compras entrantes, siempre es válido
        if ($this->order->order_type === Order::ORDER_TYPE_PURCHASE) {
            return true;
        }

        // Para ventas salientes, verificar stock disponible
        return $this->product->current_stock >= $this->quantity;
    }

    /**
     * Accessor: Get available stock for this product
     */
    public function getAvailableStockAttribute(): int
    {
        return $this->product?->current_stock ?? 0;
    }

    /**
     * Accessor: Get stock difference after this order detail
     */
    public function getStockDifferenceAttribute(): int
    {
        if (!$this->product) {
            return 0;
        }

        return $this->order->order_type === Order::ORDER_TYPE_PURCHASE 
            ? $this->quantity 
            : -$this->quantity;
    }

    /**
     * Method: Calculate line subtotal
     */
    public function calculateLineSubtotal(): float
    {
        return $this->quantity * $this->unit_price_at_order;
    }

    /**
     * Method: Update line subtotal
     */
    public function updateLineSubtotal(): void
    {
        $this->line_subtotal = $this->calculateLineSubtotal();
        $this->save();
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Calcular subtotal antes de crear
        static::creating(function ($orderDetail) {
            $orderDetail->line_subtotal = $orderDetail->calculateLineSubtotal();
        });

        // Calcular subtotal antes de actualizar
        static::updating(function ($orderDetail) {
            if ($orderDetail->isDirty(['quantity', 'unit_price_at_order'])) {
                $orderDetail->line_subtotal = $orderDetail->calculateLineSubtotal();
            }
        });
    }
}