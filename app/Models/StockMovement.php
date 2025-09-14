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
        'id_movement_type',
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
     * Get the movement type that this stock movement is associated with.
     */
    public function movementType(): BelongsTo
    {
        return $this->belongsTo(MovementType::class, 'id_movement_type');
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
     * Obtener la cantidad absoluta del movimiento
     */
    public function getAbsoluteQuantityAttribute(): int
    {
        return abs($this->quantity_moved);
    }

    public static function getMovementTypes(): array
    {
        return MovementType::all()->pluck('name')->toArray();
    }

    public static function getIncrementMovementTypes(): array
    {
        return MovementType::getIncrementMovementTypes();
    }

    public static function getDecrementMovementTypes(): array
    {
        return MovementType::getDecrementMovementTypes();
    }
}