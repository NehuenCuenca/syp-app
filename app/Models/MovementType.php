<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MovementType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'increase_stock',
    ];

    //define the inverse relationship with the stock movement model
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'id_movement_type');
    }

    /**
     * retorna aquellos tipos de movimientos que tienden a incrementar el stock (compra, ajuste positivo, devolucion de cliente)
     */
    public static function getIncrementMovementTypes(): array
    {
        return MovementType::where('increase_stock', true)->pluck('name')->toArray();
    }

    /**
     * retorna aquellos tipos de movimientos que tienden a decrementar> el stock (venta, ajuste negativo, devolucion de proveedor)
     */
    public static function getDecrementMovementTypes(): array
    {
        return MovementType::where('increase_stock', false)->pluck('name')->toArray();
    }
}
