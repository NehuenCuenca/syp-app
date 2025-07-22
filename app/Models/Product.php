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
        'category'
    ];

    protected $casts = [
        'buy_price' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'current_stock' => 'integer',
        'min_stock_alert' => 'integer'
    ];

    /**
     * Get all categories of products
     */
    public static function getCategories(): array
    {
        return Product::select('category')
                ->distinct()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->orderBy('category')
                ->pluck('category')
                ->toArray();
    }
}