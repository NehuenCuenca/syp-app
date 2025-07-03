<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'avg_purchase_price',
        'suggested_sale_price',
        'current_stock',
        'min_stock_alert',
        'category'
    ];

    protected $casts = [
        'avg_purchase_price' => 'decimal:2',
        'suggested_sale_price' => 'decimal:2',
        'current_stock' => 'integer',
        'min_stock_alert' => 'integer'
    ];
}