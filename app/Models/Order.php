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
        'id_user_creator',
        'estimated_delivery_date',
        'actual_delivery_date',
        'order_type',
        'order_status',
        'total_gross',
        'total_taxes',
        'total_net',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'estimated_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'total_gross' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'total_net' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * Constants for order types
     */
    const ORDER_TYPE_PURCHASE = 'Compra_Entrante';
    const ORDER_TYPE_SALE = 'Venta_Saliente';

    /**
     * Constants for order statuses
     */
    const STATUS_PENDING = 'Pendiente';
    const STATUS_COMPLETED = 'Completado';
    const STATUS_CANCELLED = 'Cancelado';
    const STATUS_RETURNED = 'Devuelto';

    /**
     * Get all order types
     */
    public static function getOrderTypes(): array
    {
        return [
            self::ORDER_TYPE_PURCHASE,
            self::ORDER_TYPE_SALE,
        ];
    }

    /**
     * Get all order statuses
     */
    public static function getOrderStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
            self::STATUS_RETURNED,
        ];
    }

    /**
     * Relationship: Order belongs to a Contact
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'id_contact');
    }

    /**
     * Relationship: Order belongs to a User (creator)
     */
    public function userCreator(): BelongsTo
    {
        // return $this->belongsTo(User::class, 'id_user_creator');
        return $this->belongsTo(User::class, 'id_user_creator', 'id');
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
     * Scope para filtrar pedidos exportables
     */
    public function scopeExportable($query)
    {
        return $query->where('order_type', 'Venta_Saliente')
                    ->where('order_status', 'Completado');
    }

    /**
     * Scope: Filter by order type
     */
    public function scopeOfType($query, string $orderType)
    {
        return $query->where('order_type', $orderType);
    }

    /**
     * Scope: Filter by order status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('order_status', $status);
    }

    /**
     * Scope: Filter by contact
     */
    public function scopeForContact($query, int $contactId)
    {
        return $query->where('id_contact', $contactId);
    }

    /**
     * Scope: Filter by date range
     */
    // public function scopeInDateRange($query, string $from = null, string $to = null)
    public function scopeInDateRange($query, string $from, string $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        
        return $query;
    }

    /**
     * Scope: Search in order fields
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('id', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%")
              ->orWhereHas('contact', function ($contactQuery) use ($search) {
                  $contactQuery->where('company_name', 'like', "%{$search}%")
                             ->orWhere('contact_name', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Accessor: Get formatted order type
     */
    public function getFormattedOrderTypeAttribute(): string
    {
        return match ($this->order_type) {
            self::ORDER_TYPE_PURCHASE => 'Compra Entrante',
            self::ORDER_TYPE_SALE => 'Venta Saliente',
            default => $this->order_type,
        };
    }

    /**
     * Accessor: Get formatted order status
     */
    public function getFormattedOrderStatusAttribute(): string
    {
        return match ($this->order_status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_RETURNED => 'Devuelto',
            default => $this->order_status,
        };
    }

    /**
     * Accessor: Check if order is completed
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->order_status === self::STATUS_COMPLETED;
    }

    /**
     * Accessor: Check if order is pending
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->order_status === self::STATUS_PENDING;
    }

    /**
     * Accessor: Check if order is cancelled
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->order_status === self::STATUS_CANCELLED;
    }

    /**
     * Accessor: Check if order is returned
     */
    public function getIsReturnedAttribute(): bool
    {
        return $this->order_status === self::STATUS_RETURNED;
    }

    /**
     * Accessor: Check if order is a purchase
     */
    public function getIsPurchaseAttribute(): bool
    {
        return $this->order_type === self::ORDER_TYPE_PURCHASE;
    }

    /**
     * Accessor: Check if order is a sale
     */
    public function getIsSaleAttribute(): bool
    {
        return $this->order_type === self::ORDER_TYPE_SALE;
    }

    /**
     * Accessor: Get total quantity of products in order
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->orderDetails->sum('quantity');
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
    public function calculateTotals(float $taxRate = 0.21): void
    {
        $totalGross = $this->orderDetails->sum('line_subtotal');
        $totalTaxes = $totalGross * $taxRate;
        $totalNet = $totalGross + $totalTaxes;

        $this->update([
            'total_gross' => $totalGross,
            'total_taxes' => $totalTaxes,
            'total_net' => $totalNet,
        ]);
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

    /**
     * Método para validar si puede cambiar a un estado específico
     */
    public function getShowValidTransitionsAttribute()
    {
        $validTransitions = [
            'Pendiente' => ['Completado', 'Cancelado'],
            'Completado' => ['Pendiente', 'Devuelto'],
            'Cancelado' => ['Pendiente'],
            'Devuelto' => ['Pendiente', 'Completado'],
        ];

        return $validTransitions[$this->order_status] ?? [];
    }
}