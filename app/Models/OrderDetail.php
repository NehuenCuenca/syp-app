<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_order_detail';

    protected $fillable = [
        'id_order',
        'id_product',
        'quantity',
        'unit_price_at_order',
        'line_subtotal'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price_at_order' => 'decimal:2',
        'line_subtotal' => 'decimal:2'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}