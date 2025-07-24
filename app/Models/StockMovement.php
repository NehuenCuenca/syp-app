<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'stock_movements';

    /**
     * The primary key for the model.
     */
    // protected $primaryKey = 'id_movement';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id_product',
        'id_order',
        'id_user_responsible',
        'movement_type',
        'quantity_moved',
        'movement_date',
        'external_reference',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'id_product' => 'integer',
        'id_order' => 'integer',
        'id_user_responsible' => 'integer',
        'quantity_moved' => 'integer',
        'movement_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Establecer la fecha de movimiento automáticamente si no se proporciona
        static::creating(function ($stockMovement) {
            if (!$stockMovement->movement_date) {
                $stockMovement->movement_date = now();
            }
        });
    }

    /**
     * Get the product that this stock movement belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    /**
     * Get the order that this stock movement is associated with.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    /**
     * Get the user responsible for this stock movement.
     */
    public function userResponsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_responsible');
    }

    /**
     * Scope para filtrar por tipo de movimiento
     */
    public function scopeByMovementType($query, $movementType)
    {
        return $query->where('movement_type', $movementType);
    }

    /**
     * Scope para filtrar por producto
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('id_product', $productId);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    /**
     * Scope para movimientos de incremento de stock
     */
    public function scopeIncrements($query)
    {
        return $query->whereIn('movement_type', ['Compra_Entrante', 'Devolucion_Cliente', 'Ajuste_Positivo']);
    }

    /**
     * Scope para movimientos de decremento de stock
     */
    public function scopeDecrements($query)
    {
        return $query->whereIn('movement_type', ['Venta_Saliente', 'Devolucion_Proveedor', 'Ajuste_Negativo']);
    }

    /**
     * Determinar si el movimiento es un incremento de stock
     */
    public function isIncrement(): bool
    {
        return in_array($this->movement_type, ['Compra_Entrante', 'Devolucion_Cliente', 'Ajuste_Positivo']);
    }

    /**
     * Determinar si el movimiento es un decremento de stock
     */
    public function isDecrement(): bool
    {
        return in_array($this->movement_type, ['Venta_Saliente', 'Devolucion_Proveedor', 'Ajuste_Negativo']);
    }

    /**
     * Obtener la cantidad absoluta del movimiento
     */
    public function getAbsoluteQuantityAttribute(): int
    {
        return abs($this->quantity_moved);
    }

    /**
     * Obtener descripción legible del tipo de movimiento
     */
    public function getMovementTypeDescriptionAttribute(): string
    {
        $descriptions = [
            'Compra_Entrante' => 'Compra entrante',
            'Venta_Saliente' => 'Venta saliente',
            'Ajuste_Positivo' => 'Ajuste positivo',
            'Ajuste_Negativo' => 'Ajuste negativo',
            'Devolucion_Cliente' => 'Devolución de cliente',
            'Devolucion_Proveedor' => 'Devolución a proveedor'
        ];

        return $descriptions[$this->movement_type] ?? $this->movement_type;
    }
}