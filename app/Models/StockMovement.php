<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    // protected $primaryKey = 'id_movement';

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

    protected $casts = [
        'movement_date' => 'datetime',
        'quantity_moved' => 'integer'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function userResponsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_responsible');
    }
}