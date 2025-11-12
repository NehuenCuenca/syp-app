<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'buy_price',
        'profit_percentage',
        'sale_price',
        'current_stock',
        'min_stock_alert',
        'id_category'
    ];

    protected $casts = [
        'buy_price' => 'integer',
        'profit_percentage' => 'integer',
        'sale_price' => 'integer',
        'current_stock' => 'integer',
        'min_stock_alert' => 'integer'
    ];

    protected $appends = [
        'search_alias'
    ];

    public function getIsLowStockAttribute()
    {
        return $this->current_stock <= $this->min_stock_alert;
    }

    public function getSearchAliasAttribute()
    {
        return "{$this->code}| {$this->name}";
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id');
    }

    public function calculateSellPrice()
    {
        return $this->buy_price * (1 + $this->profit_percentage / 100);
    }
}