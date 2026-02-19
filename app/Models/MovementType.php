<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MovementType extends Model
{
    use HasFactory;

    const MOVEMENT_TYPE_BUY  = 'compra';
    const MOVEMENT_TYPE_SALE = 'venta';
    const MOVEMENT_TYPE_POSITIVE_ADJUSTMENT = 'ajuste positivo';
    const MOVEMENT_TYPE_NEGATIVE_ADJUSTMENT = 'ajuste negativo';

    protected $fillable = [
        'name',
        'increase_stock',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'movement_type_id');
    }

    //define the inverse relationship with the stock movement model
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'movement_type_id');
    }

    /**
     * retorna aquellos tipos de movimientos que tienden a incrementar el stock (compra, ajuste positivo)
     */
    public static function getIncrementMovementTypes(): array
    {
        return MovementType::where('increase_stock', true)->pluck('id')->toArray();
    }

    /**
     * retorna aquellos tipos de movimientos que tienden a decrementar> el stock (venta, ajuste negativo)
     */
    public static function getDecrementMovementTypes(): array
    {
        return MovementType::where('increase_stock', false)->pluck('id')->toArray();
    }

    /**
     * retorna todos los tipos de movimientos
     */
    public static function getMovementTypes(): array
    {
        return [
            self::MOVEMENT_TYPE_BUY,
            self::MOVEMENT_TYPE_SALE,
            self::MOVEMENT_TYPE_POSITIVE_ADJUSTMENT,
            self::MOVEMENT_TYPE_NEGATIVE_ADJUSTMENT,
        ];
    }
}
