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
        'product_id',
        'id_order',
        'id_order_detail',
        'movement_type_id',
        'quantity_moved',
        'movement_date',
        'external_reference',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'product_id' => 'integer',
        'id_order' => 'integer',
        'id_order_detail' => 'integer',
        'movement_type_id' => 'integer',
        'quantity_moved' => 'integer',
        'notes' => 'string',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    protected $appends = [
        'sign_with_quantity_and_product',
        'notes_info'
    ];

    /**
     * Get the product that this stock movement belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product that this stock movement belongs to.
     */
    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class, 'id_order_detail');
    }

    /**
     * Get the movement type that this stock movement is associated with.
     */
    public function movementType(): BelongsTo
    {
        return $this->belongsTo(MovementType::class, 'movement_type_id');
    }

    /**
     * Get the order that this stock movement is associated with.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    /**
     * Scope para filtrar por producto
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
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

    public function getSignWithQuantityAndProductAttribute(): string
    {
        $productName = (!$this->product) ? 'Producto Borrado' : $this->product->name;
        $text = "{$this->quantity_moved} {$productName}";
        return ($this->quantity_moved > 0) 
                ? "+{$text}" 
                : "{$text}";
    }

    public function getNotesInfoAttribute(): string
    {
        return !empty($this->notes) ? $this->notes : 'Sin notas';
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