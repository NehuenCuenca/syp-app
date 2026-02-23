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
        'category_id'
    ];

    protected $casts = [
        'buy_price' => 'integer',
        'profit_percentage' => 'integer',
        'sale_price' => 'integer',
        'current_stock' => 'integer',
        'min_stock_alert' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected $appends = [
        'search_alias',
        'stock_availability',
        'is_low_stock',
        'is_empty_stock',
    ];

    public function getIsLowStockAttribute()
    {
        return $this->current_stock <= $this->min_stock_alert;
    }

    public function getIsEmptyStockAttribute()
    {
        return $this->current_stock <= 0;
    }

    public function getSearchAliasAttribute()
    {
        $is_deleted = ($this->trashed()) ? '(BORRADO)' : '';
        return "{$is_deleted}{$this->code}| {$this->name}";
    }

    public function getStockAvailabilityAttribute()
    {
        return ($this->current_stock > 0) ? "{$this->current_stock} disponibles" : 'Agotado';
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function calculateSellPrice()
    {
        return (int)($this->buy_price * (1 + $this->profit_percentage / 100));
    }
}