<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    // protected $primaryKey = 'id_order';

    /**
     * The table associated with the model.
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'contact_id',
        'code',
        'id_movement_type',
        'adjustment_amount',
        'subtotal',
        'total_net',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'adjustment_amount' => 'integer',
        'subtotal' => 'integer',
        'total_net' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    protected $appends = [
        'search_alias', 'order_type_with_total_net', 'is_exportable',
        'subtotal_as_currency', 'adjustment_as_currency', 'total_net_as_currency'
    ];

    /**
     * Constants for order types
     */
    const ORDER_TYPE_PURCHASE = 'Compra';
    const ORDER_TYPE_SALE = 'Venta';

        /**
     * Relationship: Order belongs to a Contact
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }

    /**
     * Relationship: Order belongs to a Movement
     */
    public function movementType(): BelongsTo
    {
        return $this->belongsTo(MovementType::class, 'id_movement_type');
    }

    public function getSearchAliasAttribute()
    {
        return "$this->code| {$this->contact->name}";
    }
    
    /**
     * Get all order types
     */
    public static function getOrderTypes(): array
    {
        return [
            MovementType::firstWhere('name', self::ORDER_TYPE_PURCHASE),
            MovementType::firstWhere('name', self::ORDER_TYPE_SALE)
        ];
    }


    /**
     * Relationship: Order has many OrderDetails
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'id_order');
    }

    /**
     * Relationship: Order has many StockMovements
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'id_order');
    }


    /**
     * Accessor: Get formatted order type
     */
    public function getFormattedOrderTypeAttribute(): string
    {
        return match ($this->movementType->name) {
            self::ORDER_TYPE_PURCHASE => 'Compra Entrante',
            self::ORDER_TYPE_SALE => 'Venta Saliente',
            default => $this->movementType->name,
        };
    }

    /**
     * Accessor: Get formatted order type 
     */
    public function getOrderTypeWithTotalNetAttribute(): string
    {
        return "{$this->movementType->name} de " . format_number_to_currency($this->total_net);
    }

    /**
     * Accessor: Get formatted order type 
     */
    public function getIsExportableAttribute(): bool
    {
        return $this->getIsSaleAttribute();
    }

    /**
     * Accessor: Get subtotal formatted as currency
     */
    public function getSubtotalAsCurrencyAttribute(): string
    {
        return format_number_to_currency($this->subtotal);
    }

    /**
     * Accessor: Get adjustment formatted as currency 
     */
    
    public function getAdjustmentAsCurrencyAttribute(): string
    {
        return format_number_to_currency($this->adjustment_amount);
    }
    
    /**
     * Accessor: Get total net formatted as currency 
     */
    public function getTotalNetAsCurrencyAttribute(): string
    {
        return format_number_to_currency($this->total_net);
    }


    /**
     * Accessor: Check if order is a purchase
     */
    public function getIsPurchaseAttribute(): bool
    {
        return $this->id_movement_type === MovementType::firstWhere('name', self::ORDER_TYPE_PURCHASE)->id;
    }

    /**
     * Accessor: Check if order is a sale
     */
    public function getIsSaleAttribute(): bool
    {
        return $this->id_movement_type === MovementType::firstWhere('name', self::ORDER_TYPE_SALE)->id;
    }

    /**
     * Accessor: Get total number of different products in order
     */
    public function getTotalProductsAttribute(): int
    {
        return $this->orderDetails->count();
    }
}