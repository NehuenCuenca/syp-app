<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'buy_price',
        'profit_percentage',
        'sale_price',
        'current_stock',
        'min_stock_alert',
        'id_category'
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'current_stock' => 'integer',
        'min_stock_alert' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id');
    }
}