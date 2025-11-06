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
        'id_contact',
        'code',
        'id_movement_type',
        'total_net',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_net' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    protected $appends = ['order_alias'];

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
        return $this->belongsTo(Contact::class, 'id_contact');
    }

    /**
     * Relationship: Order belongs to a Movement
     */
    public function movementType(): BelongsTo
    {
        return $this->belongsTo(MovementType::class, 'id_movement_type');
    }

    public function getOrderAliasAttribute()
    {
        return strtoupper($this->movementType->name) . ' ' 
             . $this->contact->company_name 
             . ' ($' . $this->total_net . ') '
             . $this->created_at->format('Y-m-d');
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

    /**
     * Method: Calculate and update order totals
     */
    public function calculateTotals(): void
    {
        $totalNet = $this->orderDetails->sum('line_subtotal');
        $this->update(['total_net' => $totalNet]);
    }

    /**
     * Method: Check if order can be edited
     */
    public function canBeEdited(): bool
    {
        // Permitir edición en todos los estados para flexibilidad
        return true;
    }

    /**
     * Method: Check if order can be deleted
     */
    public function canBeDeleted(): bool
    {
        // Permitir eliminación si no tiene movimientos de stock asociados
        // o si el usuario tiene permisos especiales
        return !$this->stockMovements()->exists() || auth()->user()?->role === 'Admin';
    }
}