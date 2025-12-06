<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NumberFormatter;

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
        'unit_price',
        'percentage_applied',
        'line_subtotal',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'percentage_applied' => 'integer',
        'line_subtotal' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    protected $appends = ['formatted_unit_price', 'formatted_line_subtotal'];


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
        return $this->belongsTo(Product::class, 'id_product')->withTrashed();
    }

    /**
     * Get the stock movements related to this order detail.
     */
    public function stockMovement()
    {
        return $this->hasOne(StockMovement::class, 'id_order_detail');
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
        return $this->formatToCurrency($this->unit_price);
    }

    /**
     * Accessor: Get product name
     */
    public function getProductNameAttribute(): string
    {
        return $this->product?->name ?? 'Producto no encontrado';
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
        if ($this->order->getIsPurchaseAttribute()) {
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
     * Accessor: Get available stock for this product
     */
    public function getFormattedLineSubtotalAttribute(): string
    {
        return $this->formatToCurrency($this->line_subtotal);
    }

    /**
     * Accessor: Get stock difference after this order detail
     */
    public function getStockDifferenceAttribute(): int
    {
        if (!$this->product) {
            return 0;
        }

        return $this->order->getIsPurchaseAttribute()
            ? $this->quantity 
            : -$this->quantity;
    }

/**
     * Method: Calculate discount subtotal
     */
    public function calculateDiscountSubtotal(): int
    {
        return (int)(($this->percentage_applied/100) * $this->quantity * $this->unit_price);
    }

    /**
     * Method: Calculate line subtotal with discount
     */
    public function calculateLineSubtotal(): int
    {
        $lineSubtotal = (int)($this->quantity * $this->unit_price);
        
        if( $this->order->getIsSaleAttribute()){
            $lineSubtotal = $lineSubtotal - $this->calculateDiscountSubtotal();
        }

        return (int)($lineSubtotal);
    }

    /**
     * Method: Update line subtotal
     */
    public function updateLineSubtotal(): void
    {
        $this->line_subtotal = $this->calculateLineSubtotal();
        $this->save();
    }

    private function formatToCurrency($amount)
    {
        $formatter = new NumberFormatter('es_AR', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);
        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '$');

        return $formatter->formatCurrency($amount, 'ARS');
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
            if ($orderDetail->isDirty(['quantity', 'unit_price', 'percentage_applied'])) {
                $orderDetail->line_subtotal = $orderDetail->calculateLineSubtotal();
            }
        });
    }
}